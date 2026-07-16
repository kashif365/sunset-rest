{{-- Orange curved divider inspired by the printed menu swooshes.
     Usage: @include('partials.curve', ['fill' => '#69001F', 'flip' => true]) --}}
<svg class="curve-divider {{ ($flip ?? false) ? 'rotate-180' : '' }}" @if($flip ?? false) style="transform: scaleY(-1);" @endif
     viewBox="0 0 1440 46" preserveAspectRatio="none" aria-hidden="true" focusable="false">
    <path d="M0,20 C240,52 480,-6 720,14 C960,34 1200,44 1440,12 L1440,46 L0,46 Z" fill="{{ $fill ?? '#F8A51B' }}" opacity="0.25"/>
    <path d="M0,30 C260,58 520,4 760,20 C1000,36 1240,46 1440,20 L1440,46 L0,46 Z" fill="{{ $fill ?? '#F8A51B' }}"/>
</svg>
