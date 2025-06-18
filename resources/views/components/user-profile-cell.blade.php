@props([
    'user' => null,
])

@if($user)
<div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded-full overflow-hidden">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
    </div>
    <div>
        <div class="font-medium">{{ $user->name }}</div>
        <div class="text-sm text-zinc-500">{{ $user->email }}</div>
    </div>
</div>
@endif 