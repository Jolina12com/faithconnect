@extends('member.dashboard_member')

@section('content')
<div class="donation-hero">
    <div class="donation-particles"></div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">


                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show donation-alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="donation-card">
                    <div class="card-glow"></div>
                    
                    @if ($errors->any())
                    <div class="alert alert-danger donation-error">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('member.donations.store') }}" method="POST" id="donationForm" class="donation-form">
                        @csrf
                        
                        <input type="hidden" name="donation_type" id="donation_type" value="">
                        
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-calendar-alt"></i>
                                <h6>Donation Information</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="donation_date" class="form-label">Donation Date *</label>
                                        <div class="input-wrapper">
                                            <input type="date" name="donation_date" id="donation_date" 
                                                class="form-control @error('donation_date') is-invalid @enderror"
                                                value="{{ old('donation_date', date('Y-m-d')) }}" required>
                                            <i class="fas fa-calendar input-icon"></i>
                                        </div>
                                        @error('donation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-gift"></i>
                                <h6>What are you donating?</h6>
                            </div>
                            <div class="donation-type-selector">
                                <label for="donation_input" class="form-label">
                                    Choose your donation type *
                                    <span class="help-text">Type to search or enter your own: Money, Rice, Clothes, Medicine, etc.</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" 
                                        id="donation_input" 
                                        class="form-control donation-input @error('donation_input') is-invalid @enderror" 
                                        placeholder="Type your donation (e.g., Money, Rice, Blankets, or anything else)"
                                        autocomplete="off"
                                        required>
                                    <i class="fas fa-search input-icon"></i>
                                </div>
                                <div id="selected-type-badge" class="selected-badge"></div>
                                @error('donation_input')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="monetary-fields" class="form-section" style="display: none;">
                            <div class="section-header monetary">
                                <i class="fas fa-money-bill-wave"></i>
                                <h6>Monetary Donation Details</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" class="form-label">Amount *</label>
                                        <div class="input-wrapper amount-input">
                                            <span class="currency-symbol">₱</span>
                                            <input type="number" name="amount" id="amount" class="form-control" 
                                                value="{{ old('amount') }}" step="0.01" min="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method" class="form-label">Payment Method *</label>
                                        <div class="input-wrapper">
                                            <select name="payment_method" id="payment_method" class="form-control">
                                                <option value="">Select Payment Method</option>
                                                <option value="Cash">💵 Cash</option>
                                                <option value="Check">📝 Check</option>
                                                <option value="Credit Card">💳 Credit Card</option>
                                                <option value="Bank Transfer">🏦 Bank Transfer</option>
                                                <option value="GCash">📱 GCash</option>
                                                <option value="PayMaya">💰 PayMaya</option>
                                                <option value="Other">🔄 Other</option>
                                            </select>
                                            <i class="fas fa-credit-card input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="physical-fields" class="form-section" style="display: none;">
                            <div class="section-header physical">
                                <i class="fas fa-box"></i>
                                <h6>Physical Donation Details</h6>
                            </div>
                            
                            <input type="hidden" name="item_name" id="item_name" value="">
                            
                            <div class="selected-item-display">
                                <i class="fas fa-box-open"></i>
                                <strong id="selected-item-display">Selected Item:</strong>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantity" class="form-label">Quantity *</label>
                                        <div class="input-wrapper">
                                            <input type="number" name="quantity" id="quantity" 
                                                class="form-control"
                                                value="{{ old('quantity') }}" step="0.01" min="0.01" placeholder="0">
                                            <i class="fas fa-hashtag input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="unit" class="form-label">Unit *</label>
                                        <div class="input-wrapper">
                                            <select name="unit" id="unit" class="form-control">
                                                <option value="">Select Unit</option>
                                                <option value="kg">⚖️ Kilograms (kg)</option>
                                                <option value="g">📏 Grams (g)</option>
                                                <option value="pieces">🔢 Pieces</option>
                                                <option value="boxes">📦 Boxes</option>
                                                <option value="packs">📦 Packs</option>
                                                <option value="cans">🥫 Cans</option>
                                                <option value="bottles">🍼 Bottles</option>
                                                <option value="pairs">👫 Pairs</option>
                                                <option value="liters">🥤 Liters</option>
                                            </select>
                                            <i class="fas fa-balance-scale input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3" id="expiry-field" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_date" class="form-label">
                                            <i class="fas fa-calendar-alt me-2"></i>Expiry Date
                                        </label>
                                        <div class="input-wrapper">
                                            <input type="date" name="expiry_date" id="expiry_date" 
                                                class="form-control" min="{{ date('Y-m-d') }}">
                                            <i class="fas fa-calendar-times input-icon"></i>
                                        </div>
                                        <small class="text-muted">Required for food and medical supplies</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3" id="condition-field" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="condition" class="form-label">
                                            <i class="fas fa-check-circle me-2"></i>Condition
                                        </label>
                                        <div class="input-wrapper">
                                            <select name="condition" id="condition" class="form-control">
                                                <option value="">Select Condition</option>
                                                <option value="new">✨ New</option>
                                                <option value="used">🔄 Used</option>
                                                <option value="good">👍 Good</option>
                                                <option value="fair">👌 Fair</option>
                                            </select>
                                            <i class="fas fa-star input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-info-circle"></i>
                                <h6>Additional Information</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category" class="form-label">Category (Optional)</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="category" id="category" class="form-control"
                                                placeholder="e.g., Tithes, Building Fund, Outreach">
                                            <i class="fas fa-tag input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <div class="input-wrapper">
                                    <textarea name="notes" id="notes" rows="3" class="form-control" 
                                        placeholder="Share any additional details about your donation...">{{ old('notes') }}</textarea>
                                    <i class="fas fa-comment input-icon"></i>
                                </div>
                            </div>

                            <div class="custom-checkbox">
                                <input type="checkbox" name="is_recurring" id="is_recurring" value="1">
                                <label for="is_recurring">
                                    <span class="checkmark"></span>
                                    <span class="checkbox-text">
                                        <strong>Recurring Donation</strong>
                                        <small>This is a regular donation I make</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="submit-btn">
                                <span class="btn-content">
                                    <i class="fas fa-heart"></i>
                                    <span>Submit Donation</span>
                                </span>
                                <div class="btn-ripple"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<style>
.donation-hero {
    background: #f8f9fa;
    position: relative;
    overflow: hidden;
    padding: 1rem 0;
}

.donation-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.051), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.15), transparent),
        radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.25), transparent);
    background-repeat: repeat;
    background-size: 100px 100px;
    animation: float 25s infinite linear;
}

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    100% { transform: translateY(-100px) rotate(360deg); }
}

.donation-header {
    color: white;
    margin-bottom: 3rem;
}

.donation-icon {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    animation: heartbeat 2s infinite;
}

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.donation-icon i {
    font-size: 3rem;
    color: #4550b0;
}

.donation-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.donation-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    font-weight: 300;
}

.donation-card {
    background: rgba(255, 255, 255, 0.822);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    padding: 3rem;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(27, 27, 156, 0.1), transparent);
    animation: rotate 10s linear infinite;
    z-index: -1;
}

@keyframes rotate {
    100% { transform: rotate(360deg); }
}

.donation-alert {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    border-left: 5px solid #28a745;
}

.donation-error {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.2);
    border-left: 5px solid #dc3545;
}

.form-section {
    margin-bottom: 2.5rem;
    padding: 2rem;
    background: rgba(248, 249, 250, 0.5);
    border-radius: 20px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.form-section:hover {
    background: rgba(248, 249, 250, 0.9);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(0, 0, 0, 0.05);
}

.section-header i {
    font-size: 1.5rem;
    margin-right: 1rem;
    color: #667eea;
    width: 30px;
    text-align: center;
}

.section-header.monetary i {
    color: #28a745;
}

.section-header.physical i {
    color: #17a2b8;
}

.section-header h6 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.help-text {
    display: block;
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 400;
    margin-top: 0.25rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem 1rem 1rem 3rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    transform: translateY(-1px);
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #6c757d;
    font-size: 1rem;
    z-index: 2;
    transition: color 0.3s ease;
}

.form-control:focus + .input-icon,
.input-wrapper:hover .input-icon {
    color: #6e7282;
}

.amount-input {
    position: relative;
}

.currency-symbol {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-weight: 600;
    color: #28a745;
    z-index: 2;
    font-size: 1.1rem;
}

.amount-input .form-control {
    padding-left: 2.5rem;
}

.donation-input {
    font-size: 1.1rem;
    padding: 1.2rem 1.2rem 1.2rem 3.5rem;
    border: 3px solid #e9ecef;
}

.donation-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.2);
}

.selected-badge {
    margin-top: 1rem;
    text-align: center;
}

.selected-badge .badge {
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.selected-item-display {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #17a2b8;
}

.selected-item-display i {
    color: #17a2b8;
    margin-right: 0.5rem;
}

.custom-checkbox {
    position: relative;
    margin: 1.5rem 0;
}

.custom-checkbox input[type="checkbox"] {
    display: none;
}

.custom-checkbox label {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.custom-checkbox label:hover {
    background: rgba(102, 126, 234, 0.05);
    border-color: #667eea;
}

.checkmark {
    width: 24px;
    height: 24px;
    border: 2px solid #ddd;
    border-radius: 6px;
    margin-right: 1rem;
    position: relative;
    transition: all 0.3s ease;
}

.custom-checkbox input:checked + label .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.custom-checkbox input:checked + label .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
}

.checkbox-text strong {
    display: block;
    color: #333;
    font-size: 1rem;
}

.checkbox-text small {
    display: block;
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.form-actions {
    text-align: center;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 2px solid rgba(0, 0, 0, 0.05);
}

.submit-btn {
    background: linear-gradient(135deg, #6b77ff, #a552ee);
    border: none;
    border-radius: 50px;
    padding: 1.2rem 3rem;
    font-size: 1.2rem;
    font-weight: 600;
    color: white;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    min-width: 200px;
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
}

.btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    position: relative;
    z-index: 2;
}

.btn-content i {
    font-size: 1.3rem;
    animation: heartbeat 2s infinite;
}

.ui-autocomplete {
    max-height: 400px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1050 !important;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.ui-menu-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.ui-menu-item:hover,
.ui-menu-item.ui-state-active,
.ui-menu-item.ui-state-focus,
.ui-menu-item.ui-state-hover {
    background: #f8f9fa !important;
    border: none !important;
    color: #333 !important;
}

.ui-menu .ui-menu-item-wrapper {
    padding: 12px 16px;
    border: none !important;
}

.ui-menu .ui-menu-item-wrapper:hover,
.ui-menu .ui-menu-item-wrapper.ui-state-active {
    background: #f8f9fa !important;
    border: none !important;
    color: #333 !important;
}

.suggestion-type {
    font-weight: 600;
    color: #333 !important;
    font-size: 18px;
    display: block;
}

.suggestion-category {
    color: #6c757d !important;
    font-size: 16px;
    display: block;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .donation-title { font-size: 2.5rem; }
    .donation-card { padding: 2rem 1.5rem; }
    .form-section { padding: 1.5rem; }
    .donation-icon { width: 80px; height: 80px; }
    .donation-icon i { font-size: 2rem; }
}
</style>

<script>
$(document).ready(function() {
    let selectedType = '';
    let selectedItemName = '';
    
    const donationTypes = {
        'monetary': {
            keywords: ['money', 'cash', 'donation', 'tithe', 'offering', 'payment', 'fund'],
            icon: '💵',
            label: 'Monetary'
        },
        'food': {
            keywords: ['rice', 'food', 'canned', 'noodles', 'sardines', 'corned', 'meat', 'vegetables', 'fruits', 'bread'],
            icon: '🍚',
            label: 'Food'
        },
        'materials': {
            keywords: ['clothes', 'blanket', 'pillow', 'mat', 'towel', 'sheets', 'materials', 'fabric', 'shoes'],
            icon: '👕',
            label: 'Materials'
        },
        'medical': {
            keywords: ['medicine', 'medical', 'vitamins', 'paracetamol', 'bandage', 'alcohol', 'thermometer'],
            icon: '💊',
            label: 'Medical'
        },
        'other': {
            keywords: ['other', 'misc', 'miscellaneous'],
            icon: '📦',
            label: 'Other'
        }
    };
    
    const itemUnits = {
        'rice': 'kg', 'sugar': 'kg', 'flour': 'kg', 'salt': 'kg', 'coffee': 'kg',
        'canned': 'cans', 'sardines': 'cans', 'corned': 'cans',
        'noodles': 'packs', 'pasta': 'packs',
        'bread': 'pieces', 'egg': 'pieces',
        'water': 'bottles', 'juice': 'bottles',
        'milk': 'liters', 'oil': 'liters',
        'clothes': 'pieces', 'shirt': 'pieces', 'pants': 'pieces', 'dress': 'pieces',
        'shoes': 'pairs', 'socks': 'pairs',
        'blanket': 'pieces', 'pillow': 'pieces', 'mat': 'pieces', 'towel': 'pieces',
        'medicine': 'boxes', 'vitamins': 'bottles', 'tablet': 'boxes', 'capsule': 'boxes',
        'syrup': 'bottles', 'alcohol': 'bottles', 'bandage': 'pieces'
    };
    
    // Cache for faster lookups
    let itemsCache = {};
    let cacheExpiry = 0;
    
    $('#donation_input').autocomplete({
        source: function(request, response) {
            const term = request.term.toLowerCase();
            const suggestions = [];
            
            // Quick local search
            for (const [type, data] of Object.entries(donationTypes)) {
                const matchedKeyword = data.keywords.find(keyword => keyword.startsWith(term));
                if (matchedKeyword) {
                    suggestions.push({
                        label: `${data.icon} ${data.label} - ${matchedKeyword}`,
                        value: matchedKeyword,
                        type: type,
                        category: 'Type',
                        itemName: null
                    });
                }
            }
            
            // Check cache
            const now = Date.now();
            if (itemsCache[term] && now < cacheExpiry) {
                const cachedItems = itemsCache[term];
                cachedItems.forEach(item => {
                    const detectedType = detectDonationType(item);
                    const typeData = donationTypes[detectedType];
                    suggestions.push({
                        label: `${typeData.icon} ${item} (${typeData.label})`,
                        value: item,
                        type: detectedType,
                        category: 'Previous Donations',
                        itemName: item
                    });
                });
                response(suggestions.slice(0, 8));
                return;
            }
            
            $.ajax({
                url: '{{ route('member.donations.items.autocomplete') }}',
                data: { term: term },
                success: function(data) {
                    itemsCache[term] = data;
                    cacheExpiry = now + 300000;
                    
                    data.forEach(item => {
                        const detectedType = detectDonationType(item);
                        const typeData = donationTypes[detectedType];
                        suggestions.push({
                            label: `${typeData.icon} ${item} (${typeData.label})`,
                            value: item,
                            type: detectedType,
                            category: 'Previous Donations',
                            itemName: item
                        });
                    });
                    
                    response(suggestions.slice(0, 8));
                }
            });
        },
        minLength: 1,
        delay: 100,
        select: function(event, ui) {
            selectedType = ui.item.type;
            selectedItemName = ui.item.itemName;
            
            $('#donation_type').val(selectedType);
            
            if (selectedType === 'monetary') {
                showMonetaryFields();
                $('#donation_input').val('Money');
            } else {
                showPhysicalFields(selectedType);
                $('#donation_input').val(ui.item.value);
                $('#item_name').val(ui.item.value);
                $('#selected-item-display').text('Selected Item: ' + ui.item.value);
                autoSelectUnit(ui.item.value);
            }
            
            const typeData = donationTypes[selectedType];
            $('#selected-type-badge').html(`
                <span class="badge bg-primary fs-6">
                    ${typeData.icon} ${typeData.label} Selected
                </span>
            `);
            
            return false;
        }
    }).data('ui-autocomplete')._renderItem = function(ul, item) {
        return $('<li>')
            .append(`
                <div>
                    <span class="suggestion-type">${item.label}</span>
                    <span class="suggestion-category">${item.category}</span>
                </div>
            `)
            .appendTo(ul);
    };
    
    function detectDonationType(itemName) {
        const lowerName = itemName.toLowerCase();
        
        for (const [type, data] of Object.entries(donationTypes)) {
            for (const keyword of data.keywords) {
                if (lowerName.includes(keyword)) {
                    return type;
                }
            }
        }
        
        return 'other';
    }
    
    function showMonetaryFields() {
        $('#monetary-fields').slideDown();
        $('#physical-fields').slideUp();
        $('#amount, #payment_method').attr('required', 'required');
        $('#item_name, #quantity, #unit').removeAttr('required');
    }
    
    function showPhysicalFields(type) {
        $('#physical-fields').slideDown();
        $('#monetary-fields').slideUp();
        $('#item_name, #quantity, #unit').attr('required', 'required');
        $('#amount, #payment_method').removeAttr('required');
        
        if (type === 'food' || type === 'medical') {
            $('#expiry-field').slideDown();
            $('#condition-field').slideUp();
        } else if (type === 'materials') {
            $('#condition-field').slideDown();
            $('#expiry-field').slideUp();
        } else {
            $('#expiry-field').slideUp();
            $('#condition-field').slideUp();
        }
    }
    
    $('#category').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route('member.donations.categories.autocomplete') }}',
                data: { term: request.term },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 1
    });
    
    function autoSelectUnit(itemName) {
        const lowerItem = itemName.toLowerCase();
        
        for (const [keyword, unit] of Object.entries(itemUnits)) {
            if (lowerItem.includes(keyword)) {
                $('#unit').val(unit);
                return;
            }
        }
        
        const type = $('#donation_type').val();
        if (type === 'food') {
            $('#unit').val('kg');
        } else if (type === 'materials') {
            $('#unit').val('pieces');
        } else if (type === 'medical') {
            $('#unit').val('boxes');
        }
    }
    
    // Allow manual input when user types without selecting from dropdown
    $('#donation_input').on('blur keyup', function() {
        const inputValue = $(this).val().trim();
        if (inputValue) {
            // Auto-detect type for manual input
            const detectedType = detectDonationType(inputValue);
            $('#donation_type').val(detectedType);
            
            if (detectedType === 'monetary') {
                showMonetaryFields();
            } else {
                showPhysicalFields(detectedType);
                $('#item_name').val(inputValue);
                $('#selected-item-display').text('Selected Item: ' + inputValue);
                autoSelectUnit(inputValue);
            }
            
            const typeData = donationTypes[detectedType];
            $('#selected-type-badge').html(`
                <span class="badge bg-secondary fs-6">
                    ${typeData.icon} ${typeData.label} (Custom)
                </span>
            `);
        }
    });

    $('#donationForm').on('submit', function(e) {
        const inputValue = $('#donation_input').val().trim();
        if (!inputValue) {
            e.preventDefault();
            alert('Please enter what you are donating');
            $('#donation_input').focus();
            return false;
        }
        
        // Auto-set donation type if not set
        if (!$('#donation_type').val()) {
            const detectedType = detectDonationType(inputValue);
            $('#donation_type').val(detectedType);
            if (detectedType !== 'monetary') {
                $('#item_name').val(inputValue);
            }
        }
        
        const donationType = $('#donation_type').val();
        
        if (donationType === 'monetary') {
            if (!$('#amount').val() || !$('#payment_method').val()) {
                e.preventDefault();
                alert('Please enter amount and payment method');
                return false;
            }
        } else {
            if (!$('#item_name').val() || !$('#quantity').val() || !$('#unit').val()) {
                e.preventDefault();
                alert('Please enter item details, quantity, and unit');
                return false;
            }
        }
    });
});
</script>
@endsection