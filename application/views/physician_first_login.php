<div class="db-content-inside si-dash">
    <div class="container">
        <div class="col-lg-6">
            <form id="form_update_password">
                <div class="form-group row">
                    <label for="">
                        Enter New Password
                    </label>
                    <input class="form-control" name="new_password" placeholder="Enter new password" type="password">
                </div>
                <div class="form-group row">
                    <label for="">
                        Re-enter New Password*
                    </label>
                    <input class="form-control" name="repeat_new_password" placeholder="Re-enter new password" type="password">
                </div>
                <div class="form-group row">
                    <button type="button" id="btn_update_password" class="btn btn-theme pull-left">Save Password</button>
                </div>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            </form>
        </div>
    </div>
</div>

