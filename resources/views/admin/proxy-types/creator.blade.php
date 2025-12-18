@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Create Proxy Type</h3>
                <p class="text-muted mb-0">Add a new proxy service category</p>
            </div>
            <a href="{{ route('admin.proxy-types.search') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.proxy-types.create') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Rotating Residential Proxies"
                                   style="border-radius: 8px; padding: 10px 14px;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="slug" class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug') }}"
                                   placeholder="e.g., rotating-residential"
                                   style="border-radius: 8px; padding: 10px 14px;"
                                   required>
                            <small class="text-muted">URL-friendly identifier (lowercase, hyphens only)</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      style="border-radius: 8px; padding: 10px 14px;"
                                      placeholder="Brief description of this proxy type">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="icon" class="form-label fw-semibold">Icon</label>
                            <input type="text"
                                   class="form-control @error('icon') is-invalid @enderror"
                                   id="icon"
                                   name="icon"
                                   value="{{ old('icon') }}"
                                   placeholder="e.g., fa-globe or /images/icons/residential.svg"
                                   style="border-radius: 8px; padding: 10px 14px;">
                            <small class="text-muted">FontAwesome class or image path</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   style="border-radius: 8px; padding: 10px 14px;">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       style="width: 3em; height: 1.5em;">
                                <label class="form-check-label fw-semibold" for="is_active" style="margin-left: 10px;">
                                    Active
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1 ms-5">Only active types are visible to users</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" style="border-radius: 8px; padding: 10px 24px; font-weight: 500;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Create Proxy Type
                            </button>
                            <a href="{{ route('admin.proxy-types.search') }}" class="btn btn-light" style="border-radius: 8px; padding: 10px 24px; border: 1px solid #e2e8f0;">
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
