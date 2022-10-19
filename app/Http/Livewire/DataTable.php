<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public bool $openingModalForm = false;

    public bool $confirmingModelDeletion = false;

    public bool $massDeletion = false;

    /**
     * @var array<int, int>
     */
    public array $selectedIdsForDeletion = [];

    /**
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'model-saved' => '$refresh',
        'model-deleted' => '$refresh',
        'models-deleted' => '$refresh',
    ];

    abstract public function openModalForm(): void;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function confirmMassDeletion(): void
    {
        $this->massDeletion = true;

        $this->confirmingModelDeletion = true;
    }

    protected function bannerEvent(string $style, string $message): void
    {
        $this->dispatchBrowserEvent('banner-message', [
            'style' => $style,
            'message' => $message,
        ]);
    }

    protected function successBannerEvent(string $message): void
    {
        $this->bannerEvent('success', $message);
    }

    protected function dangerBannerEvent(string $message): void
    {
        $this->bannerEvent('danger', $message);
    }
}
