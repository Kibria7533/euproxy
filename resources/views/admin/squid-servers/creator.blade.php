@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Add Proxy Server</h3>
                <p class="text-muted mb-0">Onboard a new Squid proxy node</p>
            </div>
            <a href="{{ route('admin.squid-servers.search') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.squid-servers.create') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="proxy_type_id" class="form-label fw-semibold">Proxy Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('proxy_type_id') is-invalid @enderror"
                                    id="proxy_type_id" name="proxy_type_id" required
                                    style="border-radius: 8px; padding: 10px 14px;">
                                <option value="">— Select proxy type —</option>
                                @foreach($proxyTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('proxy_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Which proxy product does this server serve?</small>
                            @error('proxy_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="ip" class="form-label fw-semibold">IP Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ip') is-invalid @enderror"
                                   id="ip" name="ip" value="{{ old('ip') }}"
                                   placeholder="e.g. 192.168.88.13"
                                   style="border-radius: 8px; padding: 10px 14px;" required>
                            @error('ip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="hostname" class="form-label fw-semibold">Hostname <small class="text-muted fw-normal">(optional)</small></label>
                            <input type="text" class="form-control @error('hostname') is-invalid @enderror"
                                   id="hostname" name="hostname" value="{{ old('hostname') }}"
                                   placeholder="e.g. proxy.euproxy.com"
                                   style="border-radius: 8px; padding: 10px 14px;">
                            <small class="text-muted">If set, hostname will be shown to users instead of raw IP.</small>
                            @error('hostname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="port" class="form-label fw-semibold">Port <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('port') is-invalid @enderror"
                                   id="port" name="port" value="{{ old('port', 3128) }}"
                                   min="1" max="65535"
                                   style="border-radius: 8px; padding: 10px 14px;" required>
                            @error('port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">Location <small class="text-muted fw-normal">(optional)</small></label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location" value="{{ old('location') }}"
                                   placeholder="e.g. Frankfurt, DE"
                                   style="border-radius: 8px; padding: 10px 14px;">
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Notes <small class="text-muted fw-normal">(optional)</small></label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      style="border-radius: 8px; padding: 10px 14px;"
                                      placeholder="Internal notes about this server">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       style="width: 3em; height: 1.5em;">
                                <label class="form-check-label fw-semibold" for="is_active" style="margin-left: 10px;">Active</label>
                            </div>
                            <small class="text-muted d-block mt-1 ms-5">Only active servers are shown to users.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" style="border-radius: 8px; padding: 10px 24px; font-weight: 500;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Add Server
                            </button>
                            <a href="{{ route('admin.squid-servers.search') }}" class="btn btn-light" style="border-radius: 8px; padding: 10px 24px; border: 1px solid #e2e8f0;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
