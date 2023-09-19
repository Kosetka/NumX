function citySelectionChanged() {
    var selectedCity = document.getElementById("city").value;
    console.log("Wybrano miasto: " + selectedCity);

    
    for (var i = 0; i < databaseTypes.length; i++) {
        var databaseType = databaseTypes[i];
        fetchQuantityFromDatabase(selectedCity, databaseType, 1);
        fetchQuantityFromDatabase(selectedCity, databaseType, 2);
        fetchQuantityFromDatabase(selectedCity, databaseType, 3);
        fetchQuantityFromDatabase(selectedCity, databaseType, 4);
    }
}

function updateLastAccessDate() {
    var phoneNumber = document.getElementById("phoneNumber").value;
    if (phoneNumber === "")
        phoneNumber = null;
    // Wywołaj funkcję PHP poprzez AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "./functions/update_last_access_date.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            alert(response);
        }
    };
    xhr.send("phoneNumber=" + phoneNumber);
}

var labelsArray = [];

function fetchQuantityFromDatabase(city, databaseType, action) {
    var xhr = new XMLHttpRequest();
    var url = './functions/get_quantity.php';
    var params = 'city=' + encodeURIComponent(city) + '&databaseType=' + encodeURIComponent(databaseType) + '&action=' + encodeURIComponent(action);

    xhr.open('POST', url, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var responseText = xhr.responseText;
            var quantities = JSON.parse(responseText);
            if (action == 1) {
                let allLabelId = 'all' + databaseType + 'Label';
                let allLabel = document.getElementById(allLabelId);

                if (allLabel) {
                    allLabel.innerHTML = quantities[databaseType];
                }
                sumValues("all");
            } else if (action == 2) {
                let blockedLabelId = 'blocked' + databaseType + 'Label';
                let blockedLabel = document.getElementById(blockedLabelId);

                if (blockedLabel) {
                    blockedLabel.innerHTML = quantities[databaseType];
                }
                sumValues("blocked");
            } else  if (action == 4) {
                let temporaryLabelId = 'temporary' + databaseType + 'Label';
                let temporaryLabel = document.getElementById(temporaryLabelId);

                if (temporaryLabel) {
                    temporaryLabel.innerHTML = quantities[databaseType];
                }
                sumValues("temporary");
            } else if (action == 3) {
                let quantityLabelId = 'quantity' + databaseType + 'Label';
                let quantityLabel = document.getElementById(quantityLabelId);

                let quantityId = 'quantity' + databaseType;
                let quantity = document.getElementById(quantityId);
                quantity.value = 0;

                let quantityCheckId = 'check' + databaseType;
                let quantityCheck = document.getElementById(quantityCheckId);

                if (quantityLabel) {
                    quantityLabel.innerHTML = quantities[databaseType];
                    quantity.max = quantities[databaseType];
                    if (quantities[databaseType] > 0) {
                        quantityCheck.disabled = false;
                    } else {
                        quantityCheck.disabled = true;
                    }
                }
                sumValues("quantity");
            }
        }
    };
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(params);
}

function toggleQuantityInput(checkbox) {
    var quantityInputId = "quantity" + checkbox.value;
    var quantityInput = document.getElementById(quantityInputId);
    
    if (checkbox.checked) {
        quantityInput.removeAttribute("disabled");
        quantityInput.setAttribute("required", "required");
    } else {
        quantityInput.setAttribute("disabled", "disabled");
        quantityInput.removeAttribute("required");
        quantityInput.value = 0;
    }
}

function sumValues(prefix) {
    var total = 0;
    for (var i = 0; i < databaseTypes.length; i++) {
        var labelId = prefix + databaseTypes[i] + 'Label'; 
        var labelElement = document.getElementById(labelId);
        
        if (labelElement) {
            var value = parseInt(labelElement.textContent) || 0;
            total += value;
        }
    }

    var totalLabelId = prefix + 'TotalLabel'; 
    var totalLabelElement = document.getElementById(totalLabelId);

    if (totalLabelElement) {
        totalLabelElement.textContent = total;
    }
}
