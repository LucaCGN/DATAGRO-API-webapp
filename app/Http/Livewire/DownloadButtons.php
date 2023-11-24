<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DownloadButtons extends Component
{
    public function downloadCSV()
    {
        // Implement the logic to trigger CSV download
    }

    public function downloadPDF()
    {
        // Implement the logic to trigger PDF download
    }

    public function render()
    {
        return view('livewire.download-buttons');
    }
}
