(() => {
  'use strict';

  const forms = document.querySelectorAll('.needs-validation');

  Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
              event.preventDefault();
              event.stopPropagation();
          }
          form.classList.add('was-validated');
      }, false);
  });
})();

function saveCheckboxValue(id, checked) {
  localStorage.setItem(id, checked);
}

function savepurposeCheckboxValue(id, checked) {
  localStorage.setItem(id, checked);
}
function saveInputValue(id, value) {
  localStorage.setItem(id, value);
  updateCheckboxLabel();
}

function loadInputValue(key, elementId) {
  const savedValue = localStorage.getItem(key);
  if (savedValue !== null) {
      document.getElementById(elementId).value = savedValue;
  }
}

function updateCheckboxLabel() {
  var fullName = document.getElementById('req_name').value;
  var fullNamePlaceholder = document.getElementById('full-name-placeholder');
  fullNamePlaceholder.textContent = fullName ? fullName : '[Full Name]';
}

document.addEventListener("DOMContentLoaded", function() {
  console.log("DOM fully loaded and parsed");

    const inputFields = [
      'req_name', 'req_contact_no', 'req_email', 'stud_lastname', 'stud_firstname', 
      'grade', 'section', 'sylastattended', 'level', 'relationship', 'otherDocument', 'schoolPurpose', 'othersPurpose'
    ];
    inputFields.forEach(id => {
      const input = document.getElementById(id);
      if (input) {
          input.value = localStorage.getItem(id) || '';
          validateField(input);

          input.addEventListener('input', () => {
              localStorage.setItem(id, input.value);
              validateField(input);
          });

          input.addEventListener('change', () => validateField(input));
      } else {
          console.warn(`Element with ID ${id} not found`);
      }
  });

  loadInputValue('stud_midname', 'stud_midname');
  loadInputValue('stud_suffix', 'stud_suffix');
  loadInputValue('stud_contact_no', 'stud_contact_no');
  loadInputValue('stud_email', 'stud_email');

  toggleOtherDocumentInput();
  togglePurposeInput();

  document.getElementById('flexCheckDefault10').addEventListener('change', toggleOtherDocumentInput);
  document.getElementById('flexCheckPurpose0').addEventListener('change', togglePurposeInput);
  
  const otherDocumentField = document.getElementById('otherDocument');
  otherDocumentField.value = localStorage.getItem('otherDocument') || '';
  
  const schoolPurposeField = document.getElementById('schoolPurpose');
  schoolPurposeField.value = localStorage.getItem('schoolPurpose') || '';

  otherDocumentField.addEventListener('input', () => {
      localStorage.setItem('otherDocument', otherDocumentField.value);
      validateField(otherDocumentField);
  });

  schoolPurposeField.addEventListener('input', () => {
      localStorage.setItem('schoolPurpose', schoolPurposeField.value);
      validateField(schoolPurposeField);
  });

  var reqNameInput = document.getElementById('req_name');
  reqNameInput.addEventListener('input', function() {
      saveInputValue('req_name', this.value);
  });

  var savedName = localStorage.getItem('req_name');
  if (savedName) {
      reqNameInput.value = savedName;
      updateCheckboxLabel();
  }

  const checkbox4 = document.getElementById('flexCheckPurpose4');
  checkbox4.checked = localStorage.getItem('flexCheckPurpose4') === 'true';
  togglePurposeInput(); 

  checkbox4.addEventListener('change', function() {
      localStorage.setItem('flexCheckPurpose4', checkbox4.checked);
      togglePurposeInput();
  });

  const othersInput = document.getElementById('othersPurpose');
  othersInput.value = localStorage.getItem('othersPurpose') || '';
  validateField(othersInput);

  othersInput.addEventListener('input', () => {
      localStorage.setItem('othersPurpose', othersInput.value);
      validateField(othersInput);
  });

    const checkbox10 = document.getElementById('flexCheckDefault10');
    const otherInputDiv = document.getElementById('otherDocumentInput');
    const otherInput = document.getElementById('otherDocument');

    otherInputDiv.style.display = 'block';

    checkbox10.checked = localStorage.getItem('checkbox10') === 'true';

    otherInput.value = localStorage.getItem('otherDocument') || '';

    function validateOtherDocumentInput() {
        const feedback = otherInput.nextElementSibling;
        if (checkbox10.checked) {
            const isValid = otherInput.value !== '';
            otherInput.classList.toggle('is-valid', isValid);
            otherInput.classList.toggle('is-invalid', !isValid);
            feedback.textContent = isValid ? 'Looks good' : 'Please specify the document.';
        } else {
            otherInput.classList.remove('is-valid');
            otherInput.classList.remove('is-invalid');
            feedback.textContent = '';
        }
    }

    const savedValue = localStorage.getItem('employmentType');
    if (savedValue) {
        document.querySelector(`input[name="employmentType"][value="${savedValue}"]`).checked = true;
        document.getElementById('employPurposeInput').style.display = 'block';
    } else {
        document.getElementById('employPurposeInput').style.display = 'none';
    }
    
    const radioButtons = document.querySelectorAll('input[name="employmentType"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            localStorage.setItem('employmentType', this.value);
            document.getElementById('employPurposeInput').style.display = 'block';
        });
    });

const checkbox1 = document.getElementById('flexCheckPurpose1');
const employPurposeInput = document.getElementById('employPurposeInput');

  if (checkbox1.checked) {
    employPurposeInput.style.display = 'block';
}

checkbox1.addEventListener('change', function() {
    if (this.checked) {
        localStorage.setItem('checkbox1', 'checked');
        employPurposeInput.style.display = 'block';
    } else {
        localStorage.removeItem('checkbox1');
        employPurposeInput.style.display = 'none';
    }
});

if (localStorage.getItem('checkbox1') === 'checked') {
  checkbox1.checked = true;
  employPurposeInput.style.display = 'block';
}

document.querySelector('form').addEventListener('submit', function(event) {
  const documentCheckboxes = document.querySelectorAll('input[name="docus[]"]');
  const purposeCheckboxes = document.querySelectorAll('input[name="purposes[]"]');
  let isDocumentChecked = false;
  let isPurposeChecked = false;

  documentCheckboxes.forEach(checkbox => {
      if (checkbox.checked) {
          isDocumentChecked = true;
      }
  });

  purposeCheckboxes.forEach(checkbox => {
      if (checkbox.checked) {
          isPurposeChecked = true;
      }
  });

  if (!isDocumentChecked) {
      event.preventDefault();

      const firstDocumentCheckbox = document.getElementById('flexCheckDefault5');
      if (firstDocumentCheckbox) {
          firstDocumentCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return; 
  }

  if (!isPurposeChecked) {
      event.preventDefault(); 

      const firstPurposeCheckbox = document.getElementById('flexCheckPurpose2');
      if (firstPurposeCheckbox) {
          firstPurposeCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return; 
  } else {
    document.getElementById('spinner').style.display = 'inline-block';
  }
});

    validateOtherDocumentInput();

    const confirmCheckbox = document.getElementById('confirm-checkbox');

    confirmCheckbox.checked = localStorage.getItem('confirm-checkbox') === 'true';
    validateCheckbox(confirmCheckbox);

    confirmCheckbox.addEventListener('change', function() {
        localStorage.setItem('confirm-checkbox', confirmCheckbox.checked);
        validateCheckbox(confirmCheckbox);
    });

    function validateCheckbox(checkbox) {
        if (checkbox.checked) {
            checkbox.classList.remove('is-invalid');
            checkbox.classList.add('is-valid');
        } else {
            checkbox.classList.remove('is-valid');
            checkbox.classList.add('is-invalid');
        }
    }


    // E-SIGNATURE

    (function() {
        window.requestAnimFrame = (function(callback) {
          return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function(callback) {
              window.setTimeout(callback, 1000 / 60);
            };
        })();
      
        var canvas = document.getElementById("sig-canvas");
        var ctx = canvas.getContext("2d");
        ctx.strokeStyle = "#222222";
        ctx.lineWidth = 4;
      
        var drawing = false;
        var mousePos = { x: 0, y: 0 };
        var lastPos = { x: 0, y: 0 };
      
        function resizeCanvas() {
          // Set the internal canvas resolution to match its displayed size
          canvas.width = canvas.offsetWidth;
          canvas.height = canvas.offsetHeight;
      
          ctx.strokeStyle = "#222222";
          ctx.lineWidth = 4;
        }
      
        function loadSignature() {
          var savedSignature = localStorage.getItem('signature');
          if (savedSignature) {
            var img = new Image();
            img.src = savedSignature;
            img.onload = function() {
              ctx.drawImage(img, 0, 0);
            };
          }
        }
      
        function saveSignature() {
          var dataUrl = canvas.toDataURL();
          var sigText = document.getElementById("sig-dataUrl");
          var sigImage = document.getElementById("sig-image");
          sigText.value = dataUrl;
          sigImage.setAttribute("src", dataUrl);
          localStorage.setItem('signature', dataUrl);
      
          var submitBtn = document.getElementById("sig-submitBtn");
          if (sigText.value) {
            submitBtn.disabled = true;
          }
        }
      
        function getMousePos(canvasDom, mouseEvent) {
          var rect = canvasDom.getBoundingClientRect();
          var x = mouseEvent.clientX - rect.left;
          var y = mouseEvent.clientY - rect.top;
      
          // Adjust for screen sizes 576px or less
          if (window.innerWidth <= 576) {
            x = (x / rect.width) * canvas.width;
            y = (y / rect.height) * canvas.height;
          }
      
          return { x: x, y: y };
        }
      
        function getTouchPos(canvasDom, touchEvent) {
          var rect = canvasDom.getBoundingClientRect();
          var x = touchEvent.touches[0].clientX - rect.left;
          var y = touchEvent.touches[0].clientY - rect.top;
      
          // Adjust for screen sizes 576px or less
          if (window.innerWidth <= 576) {
            x = (x / rect.width) * canvas.width;
            y = (y / rect.height) * canvas.height;
          }
      
          return { x: x, y: y };
        }
      
        function renderCanvas() {
          if (drawing) {
            ctx.beginPath();
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(mousePos.x, mousePos.y);
            ctx.stroke();
          }
        }
      
        function isCanvasBlank(canvas) {
          const blank = document.createElement('canvas');
          blank.width = canvas.width;
          blank.height = canvas.height;
          return canvas.toDataURL() === blank.toDataURL();
        }
      
        function validateSignature() {
          var validationMessage = document.getElementById("signaturevalidation");
          if (isCanvasBlank(canvas)) {
            validationMessage.style.display = "block";
            canvas.style.borderColor = "red";
            return false;
          } else {
            validationMessage.style.display = "none";
            canvas.style.borderColor = "green";
            return true;
          }
        }
      
        // Mouse events
        canvas.addEventListener("mousedown", function(e) {
          drawing = true;
          lastPos = getMousePos(canvas, e);
        }, false);
      
        canvas.addEventListener("mouseup", function(e) {
          drawing = false;
          saveSignature();
          validateSignature();
        }, false);
      
        canvas.addEventListener("mousemove", function(e) {
          if (drawing) {
            mousePos = getMousePos(canvas, e);
            renderCanvas(); // Draw as mouse moves
            lastPos = mousePos; // Update lastPos for the next segment
          }
        }, false);
      
        // Touch events
        canvas.addEventListener("touchstart", function(e) {
          e.preventDefault();
          drawing = true;
          lastPos = getTouchPos(canvas, e);
        }, false);
      
        canvas.addEventListener("touchmove", function(e) {
          e.preventDefault();
          if (drawing) {
            mousePos = getTouchPos(canvas, e);
            renderCanvas(); // Draw as touch moves
            lastPos = mousePos; // Update lastPos for the next segment
          }
        }, false);
      
        canvas.addEventListener("touchend", function(e) {
          e.preventDefault();
          drawing = false;
          saveSignature();
          validateSignature();
        }, false);
      
        // Prevent scrolling when touching the canvas
        document.body.addEventListener("touchstart", function(e) {
          if (e.target == canvas) {
            e.preventDefault();
          }
        }, false);
        document.body.addEventListener("touchend", function(e) {
          if (e.target == canvas) {
            e.preventDefault();
          }
        }, false);
        document.body.addEventListener("touchmove", function(e) {
          if (e.target == canvas) {
            e.preventDefault();
          }
        }, false);
      
        function clearCanvas() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          validateSignature();
          localStorage.removeItem('signature');
        }
      
        // Set up the UI
        var clearBtn = document.getElementById("sig-clearBtn");
        var submitBtn = document.getElementById("sig-submitBtn");
        var form = document.getElementById("form");
      
        clearBtn.addEventListener("click", function(e) {
          e.preventDefault();
          clearCanvas();
          var sigText = document.getElementById("sig-dataUrl");
          var sigImage = document.getElementById("sig-image");
          sigText.value = "";
          sigImage.setAttribute("src", "");
          submitBtn.disabled = false;
          validateForm();
        }, false);
      
        form.addEventListener("submit", function(e) {
          saveSignature();
          if (!validateSignature() || !validateForm()) {
            e.preventDefault();
          }
        });
      
        function validateForm() {
          var reqName = document.getElementById("req_name").value;
          var reqContactNo = document.getElementById("req_contact_no").value;
          var reqEmail = document.getElementById("req_email").value;
      
          if (!reqName || !reqContactNo || !reqEmail) {
            return false;
          }
      
          return true;
        }
      
        // Load the signature when the page loads
        loadSignature();
      })();
      
  
    checkbox10.addEventListener('change', () => {
        localStorage.setItem('checkbox10', checkbox10.checked);
        validateOtherDocumentInput();
    });

    otherInput.addEventListener('input', () => {
        localStorage.setItem('otherDocument', otherInput.value);
        validateOtherDocumentInput();
    });

    const schoolPurposeCheckbox = document.getElementById('flexCheckPurpose0');
    const schoolPurposeInput = document.getElementById('schoolPurpose');

    schoolPurposeCheckbox.checked = localStorage.getItem('flexCheckPurpose0') === 'true';

    schoolPurposeInput.value = localStorage.getItem('schoolPurpose') || '';

    function validateSchoolPurposeInput() {
        const feedback = schoolPurposeInput.nextElementSibling; 
        if (schoolPurposeCheckbox.checked) {
            const isValid = schoolPurposeInput.value !== '';
            schoolPurposeInput.classList.toggle('is-valid', isValid);
            schoolPurposeInput.classList.toggle('is-invalid', !isValid);
            feedback.textContent = isValid ? 'Looks good' : 'Please specify the school name.';
        } else {
            schoolPurposeInput.classList.remove('is-valid');
            schoolPurposeInput.classList.remove('is-invalid');
            feedback.textContent = '';
        }
    }

    validateSchoolPurposeInput();

    schoolPurposeCheckbox.addEventListener('change', () => {
        localStorage.setItem('flexCheckPurpose0', schoolPurposeCheckbox.checked);
        validateSchoolPurposeInput();
    });

    schoolPurposeInput.addEventListener('input', () => {
        localStorage.setItem('schoolPurpose', schoolPurposeInput.value);
        validateSchoolPurposeInput();
    });

  function validateField(input) {
    let isValid = false;
    const value = input.value.trim();
    const feedback = input.nextElementSibling;

    switch (input.id) {
      case 'req_name':
          isValid = /^[A-Za-zÀ-ÿ'’\-\s.]+$/.test(value);
          feedback.textContent = isValid ? 'Looks good' : 'Please enter your name';
          break;
      case 'stud_lastname':
          isValid = /^[A-Za-zÀ-ÿ'’\-\s.]+$/.test(value);
          feedback.textContent = isValid ? 'Looks good' : 'Please enter the last name';
          break;
      case 'stud_firstname':
          isValid = /^[A-Za-zÀ-ÿ'’\-\s.]+$/.test(value);
          feedback.textContent = isValid ? 'Looks good' : 'Please enter the first name';
          break;
      case 'grade':
          isValid = value !== '';
          feedback.textContent = isValid ? 'Looks good' : 'Please specify the grade';
          break;
      case 'section':
          isValid = value !== '';
          feedback.textContent = isValid ? 'Looks good' : 'Please specify the section';
          break;
      case 'sylastattended':
          isValid = value !== '';
          feedback.textContent = isValid ? 'Looks good' : 'Please specify the school year';
          break;
      case 'level':
          isValid = value !== '';
          feedback.textContent = isValid ? 'Looks good' : 'Please specify the level';
          break;
      case 'req_contact_no':
          isValid = /^09\d{9}$/.test(value) && /^[0-9]*$/.test(value);
          feedback.textContent = isValid ? 'Looks good' : 'Please enter a valid 11-digit contact number';
          break;
      case 'req_email':
          isValid = /^[^\s@]+@(gmail|yahoo|outlook|hotmail|icloud|protonmail|zoho|mail|gmx|aol|fastmail|yandex|tutanota|hushmail|inbox)\.(com|net|org|info|biz|co|io|me|app|tech|store|online|xyz|edu|gov|mil)$/.test(value);
          feedback.textContent = isValid ? 'Looks good' : 'Please enter a valid email address';
          break;
      case 'relationship':
          isValid = value !== '';
          feedback.textContent = isValid ? 'Looks good' : 'Please specify your relationship to the student';
          break;
      case 'otherDocument':
          const checkbox = document.getElementById('flexCheckDefault10');
          if (checkbox.checked) {
              isValid = value !== '';
              feedback.textContent = isValid ? 'Looks good' : 'Please specify the document.';
          } else {
              feedback.textContent = '';
              input.classList.remove('is-valid');
              input.classList.remove('is-invalid');
          }
          break;
      case 'schoolPurpose':
          const schoolPurposeCheckbox = document.getElementById('flexCheckPurpose0'); 
          if (schoolPurposeCheckbox.checked) {
              isValid = value !== '';
              feedback.textContent = isValid ? 'Looks good' : 'Please specify the school name.';
          } else {
              feedback.textContent = ''; 
              input.classList.remove('is-valid');
              input.classList.remove('is-invalid');
          }
          break;
      case 'othersPurpose':
          const checkbox4 = document.getElementById('flexCheckPurpose4'); 
          if (checkbox4.checked) {
              isValid = value !== '';
              feedback.textContent = isValid ? 'Looks good' : 'Please specify the purpose.';
          } else {
              feedback.textContent = '';
              input.classList.remove('is-valid');
              input.classList.remove('is-invalid');
          }
          break;  
    }
    if (isValid) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    } else {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    }
}


  function validateCheckboxes() {
    const checkboxes = [
      'flexCheckDefault1',
      'flexCheckDefault2',
      'flexCheckDefault3',
      'flexCheckDefault4',
      'flexCheckDefault5',
      'flexCheckDefault6',
      'flexCheckDefault7',
      'flexCheckDefault8',
      'flexCheckDefault9',
      'flexCheckDefault10'
    ];
    
    const anyChecked = checkboxes.some(id => document.getElementById(id).checked);
    
    checkboxes.forEach(id => {
      const checkbox = document.getElementById(id);
      checkbox.classList.toggle('is-valid', checkbox.checked);
      checkbox.classList.toggle('is-invalid', !checkbox.checked && !anyChecked);
    });

    const note = document.getElementById('checkboxNote');
    note.style.display = anyChecked ? 'none' : 'block';
  }

  function validatePurposeCheckboxes() {
    const purposes = [
        'flexCheckPurpose1',
        'flexCheckPurpose2',
        'flexCheckPurpose3',
        'flexCheckPurpose4',
        'flexCheckPurpose0'
    ];
    
    const anyPurposeChecked = purposes.some(id => document.getElementById(id)?.checked);

    purposes.forEach(id => {
        const purposeCheckbox = document.getElementById(id);
        if (purposeCheckbox) {
            purposeCheckbox.classList.toggle('is-valid', purposeCheckbox.checked);
            purposeCheckbox.classList.toggle('is-invalid', !purposeCheckbox.checked && !anyPurposeChecked);
        }
    });

    const purposeNote = document.getElementById('purposeCheckboxNote');
    if (purposeNote) {
        purposeNote.style.display = anyPurposeChecked ? 'none' : 'block';
    }
  }

  const checkboxIds = [
    'flexCheckDefault1',
    'flexCheckDefault2',
    'flexCheckDefault3',
    'flexCheckDefault4',
    'flexCheckDefault5',
    'flexCheckDefault6',
    'flexCheckDefault7',
    'flexCheckDefault8',
    'flexCheckDefault9',
    'flexCheckDefault10'
  ];

  checkboxIds.forEach(id => {
    const checkbox = document.getElementById(id);
    checkbox.checked = localStorage.getItem(id) === 'true';
    validateCheckboxes();

    checkbox.addEventListener('click', () => {
      localStorage.setItem(id, checkbox.checked);
      validateCheckboxes();
    });
  });

  const checkbox = document.getElementById('flexCheckDefault10');
  checkbox.checked = localStorage.getItem('flexCheckDefault10') === 'true';
  toggleOtherDocumentInput(); 

  checkbox.addEventListener('change', function() {
      localStorage.setItem('flexCheckDefault10', checkbox.checked);
      toggleOtherDocumentInput();
  });

  schoolPurposeCheckbox.checked = localStorage.getItem('flexCheckPurpose0') === 'true';
  togglePurposeInput();

  schoolPurposeCheckbox.addEventListener('change', function() {
      localStorage.setItem('flexCheckPurpose0', schoolPurposeCheckbox.checked);
      togglePurposeInput();
  });

  const purposeCheckboxIds = [
    'flexCheckPurpose1',
    'flexCheckPurpose2',
    'flexCheckPurpose3',
    'flexCheckPurpose4',
    'flexCheckPurpose0'
  ];

  purposeCheckboxIds.forEach(id => {
    const purposeCheckbox = document.getElementById(id);
    if (purposeCheckbox) {
      purposeCheckbox.checked = localStorage.getItem(id) === 'true';
      validatePurposeCheckboxes();

      purposeCheckbox.addEventListener('click', () => {
        localStorage.setItem(id, purposeCheckbox.checked);
        validatePurposeCheckboxes();
      });
    } else {
      console.error(`Checkbox with ID ${id} not found`);
    }
  });

  var currentStep = parseInt(localStorage.getItem("currentStep")) || 0;
  showStep(currentStep);
});

function showStep(step) {
  var steps = document.getElementsByClassName("step");
  for (var i = 0; i < steps.length; i++) {
    steps[i].style.display = "none";
  }
  steps[step].style.display = "block";
  updateProgressBar(step);
  localStorage.setItem("currentStep", step);
}

function nextStep() {
  var currentStep = parseInt(localStorage.getItem("currentStep")) || 0;
  var steps = document.getElementsByClassName("step");
  if (currentStep < steps.length - 1) {
    currentStep++;
    showStep(currentStep);
  }
}

function prevStep() {
  var currentStep = parseInt(localStorage.getItem("currentStep")) || 0;
  if (currentStep > 0) {
    currentStep--;
    showStep(currentStep);
  }
}

function updateProgressBar(step) {
  var progressItems = document.querySelectorAll("#progressbar li");
  for (var i = 0; i < progressItems.length; i++) {
    if (i <= step) {
      progressItems[i].classList.add("active");
    } else {
      progressItems[i].classList.remove("active");
    }
  }
  var percent = parseFloat(100 / (progressItems.length - 1)) * step;
  percent = percent.toFixed();
  $(".pbar").css("width", percent + "%");
}

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip(); 
});

$(document).ready(function () {
  var currentStep = parseInt(localStorage.getItem("currentStep")) || 0;
  var steps = $("fieldset").length;

  setProgressBar(currentStep);

  $(".btn .btn-primary .nextwelcome").click(function () {
    if (currentStep < steps - 1) {
      currentStep++;
      localStorage.setItem("currentStep", currentStep);
      showStep(currentStep);
    }
  });

  $(".btn .btn-primary .submit").click(function () {
    if (currentStep < steps - 1) {
      currentStep++;
      localStorage.setItem("currentStep", currentStep);
      showStep(currentStep);
    }
  });

  $(".btn .btn-secondary .previous").click(function () {
    if (currentStep > 0) {
      currentStep--;
      localStorage.setItem("currentStep", currentStep);
      showStep(currentStep);
    }
  });

  function setProgressBar(currentStep) {
    var percent = parseFloat(100 / (steps - 1)) * currentStep;
    percent = percent.toFixed();
    $(".pbar").css("width", percent + "%");
  }
});

function toggleOtherDocumentInput() {
  const checkbox = document.getElementById('flexCheckDefault10');
  const otherDocumentInput = document.getElementById('otherDocumentInput');
  const otherDocumentField = document.getElementById('otherDocument');

  if (checkbox.checked) {
      otherDocumentInput.style.display = 'block'; 
      otherDocumentField.disabled = false; 
      otherDocumentField.value = localStorage.getItem('otherDocument') || '';
  } else {
      otherDocumentInput.style.display = 'none'; 
      otherDocumentField.disabled = true; 
      otherDocumentField.value = '';
  }
}

function togglePurposeInput() {
  const checkbox = document.getElementById('flexCheckPurpose0'); 
  const additionalPurposeInput = document.getElementById('additionalPurposeInput');
  const schoolPurposeField = document.getElementById('schoolPurpose');

  if (checkbox.checked) {
      additionalPurposeInput.style.display = 'block'; 
      schoolPurposeField.disabled = false; 
      schoolPurposeField.value = localStorage.getItem('schoolPurpose') || ''; 
  } else {
      additionalPurposeInput.style.display = 'none'; 
      schoolPurposeField.disabled = true; 
      schoolPurposeField.value = ''; 
  }

  const checkbox4 = document.getElementById('flexCheckPurpose4');
  const othersInputDiv = document.getElementById('additionalOthersPurposeInput');
  const othersInput = document.getElementById('othersPurpose');

  if (checkbox4.checked) {
      othersInputDiv.style.display = 'block';
      othersInput.disabled = false;
      othersInput.value = localStorage.getItem('othersPurpose') || '';
  } else {
      othersInputDiv.style.display = 'none';
      othersInput.disabled = true;
      othersInput.value = '';
  }

  const checkbox1 = document.getElementById('flexCheckPurpose1');
  const employPurposeInput = document.getElementById('employPurposeInput');

  if (checkbox1.checked) {
      employPurposeInput.style.display = 'block';
  } else {
      employPurposeInput.style.display = 'none';
  }
}

function goToRequestTracker() {
    window.location.href = "requesttracker.php";
}

function goToHome() {
    window.location.href = "index.php";
}