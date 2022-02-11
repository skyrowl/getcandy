<div class="space-y-4">
  @foreach ($nodes as $index => $node)
    <div class="flex items-center justify-between py-2 border-t" wire:key="node_{{ $node['id'] }}">
      {{ $node['name'] }}
      <div>
        {{-- {{ $node->children_count }} --}}
      </div>
      @if($node['children_count'])
      <button wire:click="loadChildren('{{ isset($path) ? $path . '.children.' . $index : $index }}')">Load</button>
      @endif
    </div>

    @if(isset($path))
      {{ $path }}
    @endif

    <div class="ml-4">
      @if(!empty($node['children']))
        @include('adminhub::partials.collections.tree', [
          'nodes' => $node['children'] ?? [],
          'path' => isset($path) ? ($path . '.children.' . $index) : $index
        ])
      @endif
    </div>
  @endforeach
</div>