<?php

namespace App\Services\Search;

use App\Models\Discourse;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Site search over projects and discourse entries.
 *
 * The index covers every record; publish is filtered when results are
 * loaded, so a publish toggle never leaves the index stale. Saving or
 * deleting a record flushes the cached index (see the models' booted()).
 */
class SearchService
{
  protected const CACHE_KEY = 'search.index';

  /**
   * Searchable fields and their weights, per type.
   */
  protected const FIELDS = [
    'project' => [
      'title' => 10,
      'title_short' => 10,
      'location' => 5,
      'year' => 3,
      'year_works' => 3,
      'description' => 2,
      'info' => 1,
    ],
    'discourse' => [
      'title' => 10,
      'heading' => 5,
      'date' => 3,
      'description_short' => 2,
      'description' => 2,
      'info' => 1,
    ],
  ];

  protected const MODELS = [
    'project' => Project::class,
    'discourse' => Discourse::class,
  ];

  /**
   * Published matches per type, best first.
   *
   * @return array{projects: Collection, discourse: Collection}
   */
  public function search(string $query): array
  {
    $ids = ['project' => [], 'discourse' => []];
    foreach (array_keys($this->index()->search($query)) as $key) {
      [$type, $id] = explode(':', $key);
      $ids[$type][] = (int) $id;
    }

    return [
      'projects' => $this->load('project', $ids['project']),
      'discourse' => $this->load('discourse', $ids['discourse']),
    ];
  }

  public function index(): SearchIndex
  {
    $postings = Cache::rememberForever(self::CACHE_KEY, fn () => SearchIndex::build($this->documents())->postings());

    return new SearchIndex($postings);
  }

  public static function flush(): void
  {
    Cache::forget(self::CACHE_KEY);
  }

  /**
   * @return array<string, array<int, array{0: ?string, 1: int}>>
   */
  protected function documents(): array
  {
    $documents = [];

    foreach (self::MODELS as $type => $model) {
      foreach ($model::all() as $record) {
        $documents["{$type}:{$record->id}"] = array_map(
          fn (string $field, int $weight) => [(string) $record->$field, $weight],
          array_keys(self::FIELDS[$type]),
          self::FIELDS[$type]
        );
      }
    }

    return $documents;
  }

  /**
   * Published records in ranking order.
   */
  protected function load(string $type, array $ids): Collection
  {
    $model = self::MODELS[$type];
    $rank = array_flip($ids);

    return $model::whereIn('id', $ids)->where('publish', 1)->get()
      ->sortBy(fn ($record) => $rank[$record->id])
      ->values();
  }
}
