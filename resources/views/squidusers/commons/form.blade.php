@if(!empty($availableTypes) && count($availableTypes) > 0)
<div class="mb-3">
    <label for="proxy_type_id" class="form-label fw-semibold">Proxy Type <span class="text-danger">*</span></label>
    <select class="form-select @error('proxy_type_id') is-invalid @enderror"
            id="proxy_type_id" name="proxy_type_id" required
            onchange="updateBandwidthHint(this)">
        <option value="">— Select proxy type —</option>
        @foreach($availableTypes as $type)
            <option value="{{ $type['id'] }}"
                    data-available="{{ $type['available_gb'] }}"
                    {{ old('proxy_type_id') == $type['id'] ? 'selected' : '' }}>
                {{ $type['name'] }}
                @if(!is_null($type['available_gb']))
                    — {{ number_format($type['available_gb'], 3) }} GB available
                @endif
            </option>
        @endforeach
    </select>
    @error('proxy_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@endif

<div class="mb-3">
    <label for="user" class="form-label">User</label>
    <input type="text" class="form-control" id="user" name="user" value="{{ old('user',$user ?? '') }}">
</div>
<div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="password" value="{{ old('password',$password ?? '') }}">
        <button type="button" class="btn btn-outline-secondary" onclick="
            var el = document.getElementById('password');
            el.type = el.type === 'password' ? 'text' : 'password';
            this.textContent = el.type === 'password' ? '👁' : '🙈';
        ">👁</button>
    </div>
</div>
<div class="mb-3">
    <label for="fullname" class="form-label">FullName</label>
    <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname',$fullname ?? '') }}">
</div>
<div class="mb-3">
    <label for="comment" class="form-label">Comment</label>
    <input type="text" class="form-control" id="comment" name="comment" value="{{ old('comment',$comment ?? '') }}">
</div>
<div class="mb-3">
    <label for="bandwidth_limit_gb" class="form-label">
        Bandwidth Limit (GB)
        <small class="text-muted">Leave empty for unlimited</small>
    </label>
    <input type="number" class="form-control" id="bandwidth_limit_gb" name="bandwidth_limit_gb" step="0.001" min="0" value="{{ old('bandwidth_limit_gb', $bandwidth_limit_gb ?? '') }}" placeholder="e.g., 100">
    <div id="bandwidth_hint" class="text-muted small mt-1" style="display:none;"></div>
    @error('bandwidth_limit_gb')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
function updateBandwidthHint(select) {
    var opt = select.options[select.selectedIndex];
    var available = opt ? opt.getAttribute('data-available') : null;
    var hint = document.getElementById('bandwidth_hint');
    if (available !== null && available !== '') {
        hint.textContent = 'Max available for this type: ' + parseFloat(available).toFixed(3) + ' GB';
        hint.style.display = 'block';
    } else {
        hint.style.display = 'none';
    }
}
</script>
<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" {{ str_replace('1','checked',old('enabled',$enabled ?? '')) }}>
        <label class="form-check-label" for="enabled">Enabled</label>
    </div>
</div>


<button type="submit" class="btn btn-primary mb-3">{{ $submit }}</button>
