<div class="card card-block">
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>
    <div class="card card-block">
        <form method="post" id="data_form" class="card-body">
            <h5>Add Product Variation</h5>
            <p class="text-muted">Add specific variations like XL-Blue, L-Blue, M-Red</p>
            <hr>

            <!-- Multi-Attribute Variation Container -->
            <div id="variation-attributes">
                <div class="variation-attribute-group" data-index="0">
                    <div class="row">
                        <div class="col-sm-5">
                            <label class="col-form-label" for="option_id_0">Option Name <span class="text-danger">*</span></label>
                            <select name="option_id[]" id="option_id_0" class="form-control required option-select" data-index="0">
                                <option value="">Select Option (Color, Size, etc.)</option>
                                <?php foreach ($options as $row) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label class="col-form-label" for="option_value_id_0">Option Value Name <span class="text-danger">*</span></label>
                            <select name="option_value_id[]" id="option_value_id_0" class="form-control required option-value-select" data-index="0">
                                <option value="">Select Option Value first</option>
                            </select>
                        </div>
                        <div class="col-sm-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success add-option" title="Add Option">
                                <i class="ft-plus"></i>
                            </button>
                            <button type="button" class="btn btn-danger remove-option ml-2" title="Remove Option" style="display:none;">
                                <i class="ft-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mt-3">

            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="variant_name">Variation Name <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="text" placeholder="e.g., XL Large Black, Medium Red" 
                           class="form-control margin-bottom round required" name="variant_name" id="variant_name" readonly>
                    <small class="text-muted">Variation name will be generated automatically based on selected options</small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                           value="Add Product Variation" data-loading-text="Adding...">
                    <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">Reset</button>
                    <input type="hidden" value="units/create_product_variation" id="action-url">
                    <input type="hidden" name="level_type" value="3">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let attributeIndex = 0;
const maxAttributes = 5; // Maximum number of attributes per variation

function loadOptionValues(index) {
    const optionId = document.getElementById(`option_id_${index}`).value;
    const optionValueSelect = document.getElementById(`option_value_id_${index}`);
    
    if (!optionId) {
        optionValueSelect.innerHTML = '<option value="">Select Option Value first</option>';
        generateVariationName();
        return;
    }
    
    // AJAX call to get option values for this option
    $.post('<?php echo base_url('units/get_variation_options_ajax'); ?>', {
        option_id: optionId
    }, function(data) {
        const optionValues = JSON.parse(data);
        let options = '<option value="">Select Option Value</option>';
        
        optionValues.forEach(function(optionValue) {
            options += `<option value="${optionValue.id}">${optionValue.name}</option>`;
        });
        
        optionValueSelect.innerHTML = options;
        generateVariationName();
    }).fail(function() {
        optionValueSelect.innerHTML = '<option value="">Error loading option values</option>';
    });
}

function generateVariationName() {
    const attributeGroups = document.querySelectorAll('.variation-attribute-group');
    let variationParts = [];
    
    attributeGroups.forEach(function(group) {
        const index = group.dataset.index;
        const optionSelect = document.getElementById(`option_id_${index}`);
        const optionValueSelect = document.getElementById(`option_value_id_${index}`);
        
        if (optionSelect.value && optionValueSelect.value) {
            const optionText = optionSelect.options[optionSelect.selectedIndex].text;
            const optionValueText = optionValueSelect.options[optionValueSelect.selectedIndex].text;
            
            if (optionText !== 'Select Option (Color, Size, etc.)' && optionValueText !== 'Select Option Value') {
                variationParts.push(optionValueText);
            }
        }
    });
    
    document.getElementById('variant_name').value = variationParts.join(' + ');
}

function addAttributeGroup() {
    if (document.querySelectorAll('.variation-attribute-group').length >= maxAttributes) {
        alert('Maximum ' + maxAttributes + ' attributes allowed per variation');
        return;
    }
    
    attributeIndex++;
    
    const newGroup = document.createElement('div');
    newGroup.className = 'variation-attribute-group';
    newGroup.setAttribute('data-index', attributeIndex);
    newGroup.innerHTML = `
        <div class="row mt-3">
            <div class="col-sm-5">
                <label class="col-form-label" for="option_id_${attributeIndex}">Option Name <span class="text-danger">*</span></label>
                <select name="option_id[]" id="option_id_${attributeIndex}" class="form-control required option-select" data-index="${attributeIndex}">
                    <option value="">Select Option (Color, Size, etc.)</option>
                    <?php foreach ($options as $row) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    } ?>
                </select>
            </div>
            <div class="col-sm-5">
                <label class="col-form-label" for="option_value_id_${attributeIndex}">Option Value Name <span class="text-danger">*</span></label>
                <select name="option_value_id[]" id="option_value_id_${attributeIndex}" class="form-control required option-value-select" data-index="${attributeIndex}">
                    <option value="">Select Option Value first</option>
                </select>
            </div>
            <div class="col-sm-2 d-flex align-items-end">
                <button type="button" class="btn btn-success add-option" title="Add Option">
                    <i class="ft-plus"></i>
                </button>
                <button type="button" class="btn btn-danger remove-option ml-2" title="Remove Option">
                    <i class="ft-x"></i>
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('variation-attributes').appendChild(newGroup);
    updateButtonVisibility();
}

function removeAttributeGroup(button) {
    const group = button.closest('.variation-attribute-group');
    group.remove();
    generateVariationName();
    updateButtonVisibility();
}

function updateButtonVisibility() {
    const groups = document.querySelectorAll('.variation-attribute-group');
    const isOnlyOne = groups.length === 1;
    
    groups.forEach(function(group, index) {
        const removeBtn = group.querySelector('.remove-option');
        const addBtn = group.querySelector('.add-option');
        
        // Show remove button only if more than one group
        removeBtn.style.display = isOnlyOne ? 'none' : 'inline-block';
        
        // Show add button only on the last group
        addBtn.style.display = (index === groups.length - 1) ? 'inline-block' : 'none';
    });
}

function resetForm() {
    // Reset form
    document.getElementById('data_form').reset();
    
    // Remove extra attribute groups
    const groups = document.querySelectorAll('.variation-attribute-group');
    for (let i = 1; i < groups.length; i++) {
        groups[i].remove();
    }
    
    // Reset counters and visibility
    attributeIndex = 0;
    updateButtonVisibility();
    generateVariationName();
}

function validateForm() {
    const attributeGroups = document.querySelectorAll('.variation-attribute-group');
    let hasValidAttribute = false;
    let usedCombinations = [];
    
    for (let group of attributeGroups) {
        const index = group.dataset.index;
        const optionSelect = document.getElementById(`option_id_${index}`);
        const optionValueSelect = document.getElementById(`option_value_id_${index}`);
        
        if (optionSelect.value && optionValueSelect.value) {
            hasValidAttribute = true;
            
            // Check for duplicate option types
            const combination = `${optionSelect.value}`;
            if (usedCombinations.includes(combination)) {
                alert('Cannot use the same option type multiple times in one variation!');
                return false;
            }
            usedCombinations.push(combination);
        }
    }
    
    if (!hasValidAttribute) {
        alert('Please select at least one option-value combination!');
        return false;
    }
    
    const variationName = document.getElementById('variant_name').value.trim();
    if (!variationName) {
        alert('Variation name cannot be empty!');
        return false;
    }
    
    return true;
}

$(document).ready(function() {
    // Event delegation for dynamically added elements
    $(document).on('change', '.option-select', function() {
        const index = $(this).data('index');
        loadOptionValues(index);
    });
    
    $(document).on('change', '.option-value-select', function() {
        generateVariationName();
    });
    
    $(document).on('click', '.add-option', function() {
        addAttributeGroup();
    });
    
    $(document).on('click', '.remove-option', function() {
        removeAttributeGroup(this);
    });
    
    // Form submission validation
    $('#data_form').on('submit', function(e) {
        e.preventDefault(); // Always prevent default form submission
        
        if (!validateForm()) {
            return false;
        }
        
        // Show loading state
        $('#submit-data').prop('disabled', true).val('Creating...');
        
        // Get form data
        const formData = $(this).serialize();
        console.log('Submitting form data:', formData);
        
        // Submit via AJAX
        $.post('<?php echo base_url('units/create_product_variation'); ?>', formData, function(response) {
            console.log('Server response:', response);
            
            try {
                const result = JSON.parse(response);
                if (result.status === 'Success') {
                    alert('Variation created successfully!');
                    resetForm();
                    // Optionally redirect to variations list
                    window.location.href = '<?php echo base_url('units/product_variations'); ?>';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch(e) {
                console.error('Parse error:', e);
                alert('Error processing server response');
            }
        }).fail(function(xhr, status, error) {
            console.error('AJAX error:', error);
            alert('Network error: ' + error);
        }).always(function() {
            // Reset button state
            $('#submit-data').prop('disabled', false).val('Add Product Variation');
        });
        
        return false;
    });
    
    // Initial setup
    updateButtonVisibility();
});
</script>

<style>
.variation-attribute-group {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-bottom: 10px;
}

.variation-attribute-group:not(:first-child) {
    border-top: 1px dashed #dee2e6;
    padding-top: 15px;
}

.add-option, .remove-option {
    width: 40px;
    height: 38px;
    padding: 0;
}

#variant_name {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

.text-danger {
    color: #dc3545 !important;
}
</style>