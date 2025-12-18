<!-- Modal đánh giá công việc -->
<div class="modal fade" id="reviewTaskModal" tabindex="-1" aria-labelledby="reviewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="reviewTaskForm" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reviewTaskModalLabel"><i class="bi bi-card-checklist me-2"></i>Đánh giá công
                    việc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="task_id" id="reviewTaskId">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên công việc</label>
                    <input type="text" id="reviewTitle" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <input type="text" id="reviewDescription" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Người nhận</label>
                    <input type="text" id="reviewAssignee" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <input type="text" id="reviewStatus" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hạn hoàn thành</label>
                    <input type="text" id="reviewDeadline" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nội dung báo cáo</label>
                    <textarea id="reviewReportContent" class="form-control" rows="4" disabled></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Đánh giá</label>
                    <select type="text" name="result" id="reviewResult" class="form-select" required>
                        <option value="Xuất sắc">Xuất sắc</option>
                        <option value="Tốt">Tốt</option>
                        <option value="Khá">Khá</option>
                        <option value="Trung bình">Trung bình</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu đánh giá</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('reviewTaskForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'saveTaskReview');

        try {
            const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const result = await res.json();

            if (result.success) {
                alert(result.message);

                // ⭐ FIX: Tạo Bootstrap Modal instance trước khi hide
                const modalEl = document.getElementById('reviewTaskModal');
                const reviewModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

                // Đóng modal và cleanup backdrop
                modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    location.reload();
                });

                reviewModal.hide();
            } else {
                alert(result.message);
            }
        } catch (err) {
            console.error(err);
            alert('Đã xảy ra lỗi khi lưu đánh giá.');
        }
    });
</script>