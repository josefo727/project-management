<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Livewire stores every upload in a temporary location before the form validates it.
 * Without explicit rules, any file type (e.g. a PHP web shell) is accepted at that step.
 */
class LivewireTemporaryUploadRulesTest extends TestCase
{
    public function test_temporary_upload_rules_define_a_mime_whitelist(): void
    {
        $rules = config('livewire.temporary_file_upload.rules');

        $this->assertIsArray($rules);
        $this->assertNotEmpty(array_filter($rules, fn ($rule) => is_string($rule) && str_starts_with($rule, 'mimes:')));
    }

    public function test_php_files_are_rejected_at_the_temporary_upload_step(): void
    {
        $this->assertFalse($this->passes($this->realUpload('shell.php', '<?php system($_GET["c"]);')));
        $this->assertFalse($this->passes($this->realUpload('shell.phtml', '<?php echo 1;')));
        // Disguised: image extension, PHP content (MIME is sniffed from the content, as in a real request).
        $this->assertFalse($this->passes($this->realUpload('image.php.jpg', '<?php echo 1;')));
    }

    public function test_legitimate_files_are_accepted(): void
    {
        $this->assertTrue($this->passes(UploadedFile::fake()->image('photo.jpg', 64, 64)));
    }

    private function realUpload(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'lw');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }

    private function passes(UploadedFile $file): bool
    {
        $rules = config('livewire.temporary_file_upload.rules') ?? ['required', 'file', 'max:12288'];

        return Validator::make(['file' => $file], ['file' => $rules])->passes();
    }
}
