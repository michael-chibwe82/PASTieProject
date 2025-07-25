document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('uploadModal');
    const openBtn = document.getElementById('openUploadModal');
    const closeBtn = document.querySelector('.close-btn');
    const papersContainer = document.getElementById('papersContainer');
    const uploadForm = document.querySelector('.upload-form');

    // Load existing papers on page load
    fetchPapers();

    // Modal event listeners
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Form submission handler
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/front_end/api/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('File uploaded successfully!');
                    modal.style.display = 'none';
                    uploadForm.reset();
                    fetchPapers(); // Refresh the papers list
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during upload.');
            });
        });
    }

    // Function to fetch and display papers
    function fetchPapers() {
        fetch('http://localhost/PASTieProject/api/get_papers.php')
            .then(response => response.json())
            .then(papers => {
                papersContainer.innerHTML = ''; // Clear existing content
                
                papers.forEach(paper => {
                    const paperElement = document.createElement('div');
                    paperElement.className = paper.id % 2 === 0 ? 'download-course' : 'download-course prog-bg-color';
                    
                    // <a href="download.php?id=${paper.id}">${paper.title} (${paper.year}) - ${paper.semester}</a>

                    paperElement.innerHTML = `
                        <a href="download.php?id=${paper.id}">${paper.title}</a><div class="paper-actions">
                            <button onclick="downloadPaper(${paper.id})">[Download pdf]</button>
                            <button onclick="deletePaper(${paper.id})" class="delete-btn">[Delete]</button>
                        </div>
                    `;
                    
                    papersContainer.appendChild(paperElement);
                });
            })
            .catch(error => {
                console.error('Error fetching papers:', error);
            });
    }

    // Add to global scope for button onclick handlers
    window.downloadPaper = function(id) {
        window.location.href = `download.php?id=${id}`;
    };

    window.deletePaper = function(id) {
        if (confirm('Are you sure you want to delete this paper?')) {
            fetch(`delete_paper.php?id=${id}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchPapers(); // Refresh the list
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        }
    };
    // // 📌 Modal Elements
    // const openBtn = document.getElementById('openUploadModal');
    // const uploadModal = document.getElementById('uploadModal');
    // const closeBtn = document.querySelector('.close-btn');

    // if (openBtn && uploadModal) {
    //     openBtn.addEventListener("click", function () {
    //         uploadModal.style.display = 'block';
    //     });

    //     // Close modal when clicking the close button
    //     if (closeBtn) {
    //         closeBtn.addEventListener('click', () => {
    //             uploadModal.style.display = 'none';
    //         });
    //     }

    //     // Close modal when clicking outside of it
    //     window.addEventListener('click', (e) => {
    //         if (e.target === uploadModal) {
    //             uploadModal.style.display = 'none';
    //         }
    //     });
    // }

    // // ✅ Handle success message on file upload
    // const urlParams = new URLSearchParams(window.location.search);
    // if (urlParams.has('upload')) {
    //     alert('File uploaded successfully!');
    //     history.replaceState(null, '', window.location.pathname);
    // }

    // 📌 Dropdown for selecting course/year (if present on page)
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownContent = document.querySelector('.course-dropdown-content');

    if (dropdownToggle && dropdownContent) {
        dropdownToggle.addEventListener('click', function () {
            // Toggle dropdown visibility
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            // Toggle arrow direction
            this.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!dropdownContent.contains(e.target) && e.target !== dropdownToggle) {
                dropdownContent.style.display = 'none';
                dropdownToggle.classList.remove('active');
            }
        });
    }

});


// document.addEventListener("DOMContentLoaded", function(){ 

// const openBtn = document.getElementById('openUploadModal');
// const uploadModal = document.getElementById('uploadModal');
// const closeBtn = document.querySelector('.close-btn');

// openBtn.addEventListener("click", function(){
//     uploadModal.style.display = 'block';
// });
// // if(openBtn && uploadModal){
// // openBtn.addEventListener("click", function(){
// //     uploadModal.classList.toggle('active');
// // });
// // }

// closeBtn.addEventListener('click', () => {
//     uploadModal.style.display = 'none';
// });

// window.addEventListener('click', (e) => {
//     if (e.target === uploadModal) {
//         uploadModal.style.display = 'none';
//     }
// });

// // Handle success message
// const urlParams = new URLSearchParams(window.location.search);
// if (urlParams.has('upload')) {
//     alert('File uploaded successfully!');
//     history.replaceState(null, '', window.location.pathname);
// }


// //Year Dropdown-Content
// //Close dropdownContent When you click anywhere outside
//     document.querySelector('.dropdown-toggle').addEventListener('click', function(){
//     //Toggle Dropdown Visibility
//     const dropdownContent = this.nextElementSibling;
//     dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';

//     //Toggle arrow direction
//     this.classList.toggle('active');
// });

// const dropdownToggle = document.querySelector('.dropdown-toggle');
// const dropdownContent = document.querySelector('.course-dropdown-content');
// document.addEventListener('click', function(e){
//     if(!dropdownContent.contains(e.target) && e.target !== dropdownToggle) {
//         dropdownContent.style.display = 'none';
//         dropdownToggle.classList.remove('active');
//     }
// });

// // JavaScript code Snipet for the Admin Form Submission

// });
