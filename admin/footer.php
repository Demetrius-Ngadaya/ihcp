            <footer class="mt-5 py-3 text-center text-muted">
                <p class="mb-0">&copy; <?= date('Y') ?> Global Path Insights Limited. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('.sidebar').toggleClass('active');
            });
            
            // Form validation
            $('form.needs-validation').on('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
            
            // Confirm before delete
            $('.delete-btn').on('click', function() {
                return confirm('Are you sure you want to delete this item?');
            });
        });
    </script>
</body>
</html>