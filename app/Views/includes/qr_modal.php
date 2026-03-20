
<!-- Share QR Modal -->
<div id="share-qr-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title font-18 text-white" id="qr-modal-title">Share QR</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="qr-container-wrapper p-3 bg-white rounded shadow-sm mb-3 d-inline-block">
                    <div id="qr-modal-container"></div>
                </div>
                <div class="mt-2">
                    <h5 class="mb-1 text-dark fw-bold" id="qr-modal-item-title">Access QR Code</h5>
                    <p class="text-muted mb-3 small">Scan this code with your phone to access this item immediately.</p>

                    <div class="row g-0 py-2 border-top border-bottom bg-light rounded">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold">Size</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-size">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase font-10 fw-bold">Date</small>
                            <span class="fw-semibold text-primary font-14" id="qr-modal-date">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary px-4 rounded-pill" onclick="window.print()">
                    <i class="mdi mdi-printer me-1"></i>Print QR
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #qr-modal-container img {
        margin: 0 auto;
        display: block;
        max-width: 100%;
        height: auto;
    }
    .qr-container-wrapper {
        border: 1px solid #eee;
    }
</style>
