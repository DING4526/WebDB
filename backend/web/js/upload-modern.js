/* ===== Modern Upload Auto-submit JavaScript (Unified) ===== */
/* Used by: personalwork/index.php, teamwork/index.php, war-event/_workspace.php, war-person/_workspace.php */

// Personal work upload
document.addEventListener('DOMContentLoaded', function() {
  var fileInput = document.getElementById('fileInput');
  var fileTrigger = document.getElementById('fileTrigger');
  var fileName = document.getElementById('fileName');
  var uploadForm = document.getElementById('uploadForm');
  
  if (fileTrigger && fileInput) {
    fileTrigger.addEventListener('click', function() {
      fileInput.click();
    });
    
    fileInput.addEventListener('change', function() {
      if (fileInput.files && fileInput.files.length > 0) {
        var file = fileInput.files[0];
        if (fileName) fileName.textContent = file.name;
        if (uploadForm) uploadForm.submit();
      }
    });
  }
});

// Team work upload
document.addEventListener('DOMContentLoaded', function() {
  var twFileInput = document.getElementById('twFileInput');
  var twFileTrigger = document.getElementById('twFileTrigger');
  var twFileName = document.getElementById('twFileName');
  var twUploadForm = document.getElementById('twUploadForm');
  
  if (twFileTrigger && twFileInput) {
    twFileTrigger.addEventListener('click', function() {
      twFileInput.click();
    });
    
    twFileInput.addEventListener('change', function() {
      if (twFileInput.files && twFileInput.files.length > 0) {
        var file = twFileInput.files[0];
        if (twFileName) twFileName.textContent = file.name;
        if (twUploadForm) twUploadForm.submit();
      }
    });
  }
});

// War-event/war-person upload
(function() {
  var fileInput = document.getElementById('we3-upload-input');
  var uploadBtn = document.getElementById('we3-upload-btn');
  var filenameSpan = document.getElementById('we3-upload-filename');
  var uploadForm = document.getElementById('we3-upload-form');
  
  if (uploadBtn && fileInput) {
    uploadBtn.addEventListener('click', function(e) {
      e.preventDefault();
      fileInput.click();
    });
    
    fileInput.addEventListener('change', function() {
      if (fileInput.files && fileInput.files.length > 0) {
        var file = fileInput.files[0];
        if (filenameSpan) filenameSpan.textContent = file.name;
        if (uploadForm) uploadForm.submit();
      }
    });
  }
})();
