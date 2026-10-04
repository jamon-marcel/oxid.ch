<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Api\MediaController;
use Illuminate\Http\UploadedFile;

/**
 * The uploader writes straight into storage/app/public/uploads (not through
 * the faked disk), so every stored file is removed again in tearDown.
 */
class MediaUploadTest extends AdminTestCase
{
  protected array $stored = [];

  protected function tearDown(): void
  {
    foreach ($this->stored as $name) {
      @unlink(storage_path('app/public/uploads/' . $name));
    }

    parent::tearDown();
  }

  protected function upload(UploadedFile $file)
  {
    $response = $this->admin()->postJson('/api/media/upload', ['file' => $file]);

    if ($name = $response->json('name')) {
      $this->stored[] = $name;
    }

    return $response;
  }

  public function test_an_image_is_stored_under_a_unique_sanitized_name(): void
  {
    $response = $this->upload(UploadedFile::fake()->image('Haus am See (1).JPG', 400, 300))
      ->assertOk()
      ->assertJson(['filetype' => 'jpg', 'orientation' => 'l']);

    $name = $response->json('name');
    $this->assertMatchesRegularExpression('/^[0-9a-f]{13}_haus-am-see-1\.jpg$/', $name);
    $this->assertFileExists(storage_path('app/public/uploads/' . $name));
  }

  public function test_orientation_comes_from_the_image_size(): void
  {
    $this->upload(UploadedFile::fake()->image('hoch.png', 300, 400))
      ->assertOk()
      ->assertJson(['filetype' => 'png', 'orientation' => 'p']);
  }

  public function test_a_pdf_has_no_orientation(): void
  {
    $this->upload(UploadedFile::fake()->create('plan.pdf', 20, 'application/pdf'))
      ->assertOk()
      ->assertJson(['filetype' => 'pdf', 'orientation' => '']);
  }

  public function test_other_file_types_are_refused(): void
  {
    $this->upload(UploadedFile::fake()->create('notes.txt', 1, 'text/plain'))
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['file' => 'Dateityp nicht erlaubt']);
  }

  public function test_an_image_with_a_php_name_is_refused(): void
  {
    $this->upload(UploadedFile::fake()->image('shell.php'))
      ->assertUnprocessable()
      ->assertJsonValidationErrors('file');
  }

  public function test_files_over_the_limit_are_refused(): void
  {
    $this->upload(UploadedFile::fake()->image('gross.jpg')->size(MediaController::MAX_KB + 1))
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['file' => 'Datei ist zu gross']);
  }

  public function test_a_file_is_required(): void
  {
    $this->admin()->postJson('/api/media/upload')
      ->assertUnprocessable()
      ->assertJsonValidationErrors(['file' => 'Keine Datei erhalten.']);
  }
}
