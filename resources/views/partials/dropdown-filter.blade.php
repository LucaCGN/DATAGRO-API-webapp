<!DOCTYPE html>
<div class="animated" id="dropdown-filter">
    <select class="button" id="produto-select">
        <!-- Produto options populated server-side -->
    </select>
    <select class="button" id="subproduto-select">
        <!-- SubProduto options populated server-side -->
    </select>
    <select class="button" id="local-select">
        <!-- Local options populated server-side -->
    </select>
    <select class="button" id="freq-select">
        <!-- Frequência options populated server-side -->
    </select>
    <select class="button" id="proprietario-select">
        <!-- Proprietário options populated server-side -->
    </select>
</div>
<script src="{{ asset('js/dropdown-filter.js') }}"></script>
<script>console.log('[dropdown-filter.blade.php] Dropdown filter view loaded');</script>
