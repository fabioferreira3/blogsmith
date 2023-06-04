<?php

namespace App\Http\Livewire;

use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use WireUi\Traits\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TrashTable extends DataTableComponent
{
    use Actions;

    protected $model = Document::class;
    protected $repo;

    public function configure(): void
    {
        $this->repo = new DocumentRepository();
        $this->setPrimaryKey('id');
        $this->setRefreshTime(8000);
    }

    public function restoreDoc($documentId)
    {
        try {
            $this->repo->restore($documentId);
            $this->notification(['icon' => 'success', 'iconColor' => 'text-green-400', 'timeout' => 5000, 'title' => 'Document restored!']);
        } catch (Exception) {
            $this->notification(['icon' => 'error', 'iconColor' => 'text-red-700', 'timeout' => 5000, 'title' => 'There was an error while restoring this document']);
        }
    }

    public function builder(): Builder
    {
        return Document::query()->onlyTrashed()->latest();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->format(fn ($value, $row) => $row->id)
                ->hideIf(true),
            Column::make("Type", "type")
                ->format(function ($value, $row) {
                    return view('livewire.tables.my-documents.document-type', ['type' => $row->type]);
                })
                ->searchable()
                ->sortable(),
            Column::make("Title", "title")
                ->format(function ($value, $row) {
                    return $value ? Str::limit($value, 20, '...') : "";
                })
                ->searchable()
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Language", "language")
                ->format(fn ($value, $row) => $row->language->label())
                ->searchable()
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Created at", "created_at")
                ->format(fn ($value, $row) => $row->created_at->setTimezone(session('user_timezone') ?? 'America/New_York')->format('m/d/Y - h:ia'))
                ->sortable()
                ->collapseOnMobile(),
            Column::make('Actions')
                ->label(
                    fn ($row, Column $column) => view('livewire.tables.my-documents.view-action-trash', ['rowId' => $row->id])
                ),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectFilter::make('Type')
                ->options([
                    'blog_post' => 'Blog Post'
                ])
                ->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('type', $value);
                }),
            MultiSelectFilter::make('Languages')
                ->options(
                    [
                        'en' => 'English',
                        'ar' => 'Arabic',
                        'ch' => 'Chinese',
                        'fr' => 'French',
                        'de' => 'German',
                        'el' => 'Greek',
                        'it' => 'Italian',
                        'ja' => 'Japanese',
                        'ko' => 'Korean',
                        'pl' => 'Polnish',
                        'pt' => 'Portuguese',
                        'ru' => 'Russian',
                        'es' => 'Spanish',
                        'tr' => 'Turkish'
                    ]
                )->filter(function (Builder $builder, array $value) {
                    $builder->whereIn('language', $value);
                }),
        ];
    }
}