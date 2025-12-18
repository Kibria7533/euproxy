@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">Edit Proxy Plan</h3>
                <p class="text-muted mb-0">Update pricing plan and bandwidth allocation</p>
            </div>
            <a href="{{ route('admin.proxy-plans.search') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.proxy-plans.modify', $plan->id) }}" id="planForm">
                        @csrf

                        <h5 class="fw-bold mb-3">Basic Information</h5>

                        <div class="mb-4">
                            <label for="proxy_type_id" class="form-label fw-semibold">Proxy Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('proxy_type_id') is-invalid @enderror"
                                    id="proxy_type_id"
                                    name="proxy_type_id"
                                    style="border-radius: 8px; padding: 10px 14px;"
                                    required>
                                <option value="">Select Proxy Type</option>
                                @foreach($proxyTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('proxy_type_id', $plan->proxy_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proxy_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $plan->name) }}"
                                   placeholder="e.g., 5 GB Plan"
                                   style="border-radius: 8px; padding: 10px 14px;"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="bandwidth_gb" class="form-label fw-semibold">Bandwidth (GB) <span class="text-danger">*</span></label>
                                <input type="number"
                                       class="form-control @error('bandwidth_gb') is-invalid @enderror"
                                       id="bandwidth_gb"
                                       name="bandwidth_gb"
                                       value="{{ old('bandwidth_gb', $plan->bandwidth_gb) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 5.00"
                                       style="border-radius: 8px; padding: 10px 14px;"
                                       required>
                                @error('bandwidth_gb')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="validity_days" class="form-label fw-semibold">Validity (Days)</label>
                                <input type="number"
                                       class="form-control @error('validity_days') is-invalid @enderror"
                                       id="validity_days"
                                       name="validity_days"
                                       value="{{ old('validity_days', $plan->validity_days) }}"
                                       min="1"
                                       placeholder="Leave empty for unlimited"
                                       style="border-radius: 8px; padding: 10px 14px;">
                                <small class="text-muted">Leave empty if plan expires only when bandwidth depletes</small>
                                @error('validity_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 mt-4">Pricing</h5>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="base_price" class="form-label fw-semibold">Base Price ($) <span class="text-danger">*</span></label>
                                <input type="number"
                                       class="form-control @error('base_price') is-invalid @enderror"
                                       id="base_price"
                                       name="base_price"
                                       value="{{ old('base_price', $plan->base_price) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 50.00"
                                       style="border-radius: 8px; padding: 10px 14px;"
                                       required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="discount_percentage" class="form-label fw-semibold">Discount (%)</label>
                                <input type="number"
                                       class="form-control @error('discount_percentage') is-invalid @enderror"
                                       id="discount_percentage"
                                       name="discount_percentage"
                                       value="{{ old('discount_percentage', $plan->discount_percentage) }}"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       placeholder="e.g., 20"
                                       style="border-radius: 8px; padding: 10px 14px;">
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 mt-4">Plan Attributes</h5>

                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1" {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="is_popular">
                                        Popular
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_free_trial" name="is_free_trial" value="1" {{ old('is_free_trial', $plan->is_free_trial) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="is_free_trial">
                                        Free Trial
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_renewable" name="is_renewable" value="1" {{ old('is_renewable', $plan->is_renewable) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="is_renewable">
                                        Renewable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold ms-2" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', $plan->sort_order) }}"
                                   style="border-radius: 8px; padding: 10px 14px; max-width: 200px;">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="fw-bold mb-3 mt-4">Plan Features</h5>

                        <div id="featuresContainer">
                            <!-- Existing features will be loaded here -->
                        </div>

                        <button type="button" class="btn btn-light mb-4" onclick="addFeature()" style="border-radius: 8px; padding: 8px 16px; border: 2px dashed #cbd5e1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add Feature
                        </button>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; font-weight: 500;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                                Update Plan
                            </button>
                            <a href="{{ route('admin.proxy-plans.search') }}" class="btn btn-light" style="border-radius: 8px; padding: 10px 24px; border: 1px solid #e2e8f0;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if($plan->orders()->count() > 0)
            <div class="alert alert-warning mt-3" style="border-radius: 8px;">
                <strong>Warning:</strong> This plan has {{ $plan->orders()->count() }} order(s). Changing pricing or bandwidth may not affect existing subscriptions.
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Current Stats</h5>
                    <div class="mb-3">
                        <div class="text-muted small">Final Price</div>
                        <div class="h4 mb-0">${{ number_format($plan->final_price, 2) }}</div>
                        @if($plan->discount_percentage > 0)
                            <span class="badge bg-danger">-{{ $plan->discount_percentage }}% off</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Price per GB</div>
                        <div class="h5 mb-0">${{ number_format($plan->price_per_gb, 2) }}/GB</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Total Orders</div>
                        <div class="h5 mb-0">{{ $plan->orders()->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let featureIndex = 0;
const existingFeatures = @json($plan->features);

function addFeature(key = '', value = '', label = '') {
    const container = document.getElementById('featuresContainer');
    const featureHtml = `
        <div class="card mb-3 feature-item" style="border-radius: 8px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-semibold mb-0">Feature #${featureIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-light" onclick="removeFeature(this)" style="border-radius: 6px; padding: 4px 12px; color: #dc2626;">
                        Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text"
                               class="form-control form-control-sm"
                               name="features[${featureIndex}][feature_key]"
                               placeholder="Key (e.g., max_requests)"
                               value="${key}"
                               style="border-radius: 6px;">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text"
                               class="form-control form-control-sm"
                               name="features[${featureIndex}][feature_value]"
                               placeholder="Value (e.g., unlimited)"
                               value="${value}"
                               style="border-radius: 6px;">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text"
                               class="form-control form-control-sm"
                               name="features[${featureIndex}][display_label]"
                               placeholder="Label (e.g., Unlimited Requests)"
                               value="${label}"
                               style="border-radius: 6px;">
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', featureHtml);
    featureIndex++;
}

function removeFeature(button) {
    button.closest('.feature-item').remove();
}

// Load existing features on page load
document.addEventListener('DOMContentLoaded', function() {
    if (existingFeatures.length > 0) {
        existingFeatures.forEach(feature => {
            addFeature(feature.feature_key, feature.feature_value, feature.display_label);
        });
    } else {
        // Add one empty feature if none exist
        addFeature();
    }
});
</script>
@endsection
