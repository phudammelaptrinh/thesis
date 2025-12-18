<!-- Modal xem task -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTaskModalLabel">Chi tiết Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="viewTaskId">
                <div class="mb-2"><strong>Tên Task:</strong> <span id="viewTitle"></span></div>
                <div class="mb-2"><strong>Mô tả:</strong> <span id="viewDescription"></span></div>
                <div class="mb-2"><strong>Người nhận:</strong> <span id="viewAssignee"></span></div>
                <div class="mb-2"><strong>Trạng thái:</strong> <span id="viewStatus"></span></div>
                <div class="mb-2"><strong>Deadline:</strong> <span id="viewDeadline"></span></div>
                <div class="mb-2"><strong>Báo cáo:</strong> <span id="viewReportContent"></span></div>
                <div class="mb-2"><strong>File:</strong> <span id="viewReportFile"></span></div>
                <div class="mb-2"><strong>Kết quả:</strong> <span id="viewResult"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>