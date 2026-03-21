<!-- Global Upload Modal -->
<div id="global-upload-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h4 class="modal-title font-18 text-white"><i class="mdi mdi-cloud-upload me-2"></i>Upload Center</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-bordered nav-justified bg-light" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active py-3" id="modal-upload-files-tab" data-bs-toggle="tab" href="#modal-recent-files" role="tab">
                            <i class="mdi mdi-file-plus-outline font-18 me-1"></i>Upload Files
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link py-3" id="modal-upload-texts-tab" data-bs-toggle="tab" href="#modal-recent-texts" role="tab">
                            <i class="mdi mdi-text-box-plus-outline font-18 me-1"></i>Add Text
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-4">
                    <!-- File Upload Tab -->
                    <div class="tab-pane show active" id="modal-recent-files" role="tabpanel">
                        <div class="alert alert-info py-2 mb-3 small">
                            <i class="mdi mdi-information me-1"></i>
                            Drag and drop files here or click to browse. Max 50MB per file.
                        </div>
                        <div class="dropzone-container">
                            <form action="<?php echo base_url('home/file/upload'); ?>" class="dropzone" id="global-file-dropzone">
                                <div class="fallback"><input name="file" type="file" multiple /></div>
                                <div class="dz-message needsclick py-4">
                                    <i class="mdi mdi-cloud-upload display-4 text-primary"></i>
                                    <h5>Drop files here or click to upload</h5>
                                </div>
                            </form>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold small">Expiration Policy</label>
                            <select class="form-select form-select-sm" id="global-file-expiration">
                                <option value="0">Keep Forever</option>
                                <option value="1">Expire in 1 Hour</option>
                                <option value="2">Burn After Reading</option>
                            </select>
                        </div>
                        <div id="global-upload-progress" class="mt-3" style="display:none;">
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="global-overall-bar" style="width: 0%"></div>
                            </div>
                            <p class="text-center text-muted small mt-1 mb-0" id="global-upload-status">Uploading...</p>
                        </div>
                    </div>

                    <!-- Text Upload Tab -->
                    <div class="tab-pane" id="modal-recent-texts" role="tabpanel">
                        <div class="mb-3">
                            <label for="modal_text_title" class="form-label fw-semibold">Text Title</label>
                            <input type="text" class="form-control" id="modal_text_title" placeholder="Enter title (optional)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content</label>
                            <textarea id="modal_summernote" name="text_content"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Expiration Policy</label>
                            <select class="form-select" id="modal_text_expiration">
                                <option value="0">Keep Forever</option>
                                <option value="1">Expire in 1 Hour</option>
                                <option value="2">Burn After Reading</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary px-4" id="modal_save_text_btn">
                                <i class="mdi mdi-content-save me-1"></i>Save Text
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
