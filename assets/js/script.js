jQuery(document).ready(function ($) {
    $('#jobTable').DataTable({
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        }
    });
});




document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editModal');
    const closeBtn = document.querySelector('.close');
    const editLinks = document.querySelectorAll('.edit-entry');
    const form = document.getElementById('editJobForm');

    // Open modal and populate form
    editLinks.forEach(link => {
        link.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('entry_id').value = data.id;
            document.getElementById('update_entry_nonce').value = data.nonce;
            document.getElementById('jobtitle').value = data.jobtitle;
            document.getElementById('noofvacancy').value = data.noofvacancy;
            document.getElementById('lastdatetoapply').value = data.lastdatetoapply;
            document.getElementById('applylink').value = data.applylink;
            document.getElementById('officialNotification').value = data.officialnotification;
            document.getElementById('resultlink').value = data.resultlink || '';
            document.getElementById('admitcardlink').value = data.admitcardlink || '';
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
            document.getElementById('jobtitle').focus();
        });
    });

    // Close modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        form.reset();
    });

    // Close modal on outside click
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            form.reset();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            form.reset();
        }
    });
});