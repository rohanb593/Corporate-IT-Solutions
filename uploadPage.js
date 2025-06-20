document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('mediaFiles');
    const fileInfo = document.getElementById('fileInfo');
    const previewContainer = document.getElementById('previewContainer');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const messageDiv = document.getElementById('message');

    // File selection handler
    fileInput.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        
        if (this.files.length > 0) {
            fileInfo.textContent = `${this.files.length} file(s) selected`;
            
            // Create previews
            Array.from(this.files).forEach(file => {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (file.type.startsWith('image/')) {
                        previewItem.innerHTML = `<img src="${e.target.result}" alt="${file.name}">`;
                    } else if (file.type.startsWith('video/')) {
                        previewItem.innerHTML = `
                            <video controls>
                                <source src="${e.target.result}" type="${file.type}">
                            </video>
                        `;
                    }
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = () => previewItem.remove();
                    
                    previewItem.appendChild(removeBtn);
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
        } else {
            fileInfo.textContent = 'No files selected';
        }
    });

    // Form submission handler
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        if (fileInput.files.length === 0) {
            showMessage('Please select at least one file', 'error');
            return;
        }

        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        messageDiv.style.display = 'none';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'uploadHandler.php', true);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressText.textContent = percent + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showMessage('Files uploaded successfully!', 'success');
                    uploadForm.reset();
                    fileInfo.textContent = 'No files selected';
                    previewContainer.innerHTML = '';
                } else {
                    showMessage(response.message || 'Error uploading files', 'error');
                }
            } else {
                showMessage('Error uploading files', 'error');
            }
            progressContainer.style.display = 'none';
        };

        xhr.send(formData);
    });

    function showMessage(text, type) {
        messageDiv.textContent = text;
        messageDiv.className = `message ${type}`;
        messageDiv.style.display = 'block';
    }
});