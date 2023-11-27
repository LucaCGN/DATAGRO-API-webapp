<div>
    <!-- Dropdown filters for Classificação, Subproduto, and Local -->
    <select wire:model="selectedClassificacao">
        <option value="">Select Classificação</option>
        @foreach($classifications as $classification)
            <option value="{{ $classification }}">{{ $classification }}</option>
        @endforeach
    </select>

    <select wire:model="selectedSubproduto">
        <option value="">Select Subproduto</option>
        @foreach($subproducts as $subproduct)
            <option value="{{ $subproduct }}">{{ $subproduct }}</option>
        @endforeach
    </select>

    <select wire:model="selectedLocal">
        <option value="">Select Local</option>
        @foreach($locations as $location)
            <option value="{{ $location }}">{{ $location }}</option>
        @endforeach
    </select>
</div>
