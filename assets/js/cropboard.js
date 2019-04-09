var cropper = null;
var fileList = null;
var current = 0;
var cropRect = null;
var auto = false;
var drag_mode = 'move';
var activeAutoFill = false;
var uploadingFile = false;
var tiff_image = null;
var imageObj = $('#cropboard');
var btn_autofill = null;
var cropper_activated = false;

function convertFileToDataURLviaFileReader(url) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function () {
        var reader = new FileReader();
        reader.onload = function (e) {
            var image = new Tiff({
                buffer: e.target.result
            });
            tiff_image = image;
            fileList.length = tiff_image.countDirectory();
            set_load_tiff_page(0);
        };
        reader.readAsArrayBuffer(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}
function set_load_tiff_page(tmp_current) {

    if (cropper) {
        cropper.destroy();
    }
    page_num = tmp_current;
    console.log("method set_load_tiff_page called");
    activeAutoFillButton(false);
    updatePageButton(page_num);
    tiff_image.setDirectory(page_num);
    var temp_canvas = tiff_image.toCanvas();
    imageObj.attr('src', temp_canvas.toDataURL());

    if (cropper_activated) {
        cropper.destroy();
        createCropper();
        setTimeout(function () {
            cropper.setCropBoxData(global_data.crop_data);
            cropper.rotate(global_data.crop_rotate);
        }, 100);
    }
}

function exit() {
    imageObj.attr("src", "");
    tiff_image = null;
}

function init(fileURL) {
    console.log("method init called");
    if (cropper) {
        cropper.destroy();
    }
    cropper = null;
    fileList = null;
    current = 0;
    cropRect = null;
    auto = false;
    drag_mode = 'move';
    //setup image data
    fileList = {};   //this.files for png jpeg files;
    fileList.length = 1;
    current = 0;
    updatePageButton(current);
    convertFileToDataURLviaFileReader(fileURL);
}
function createCropper() {
    console.log("method createCropper called");
    if (cropper) {
        cropper.destroy();
    }
    // console.log("outside");
    cropper = new Cropper(document.querySelector('#cropboard'), {
        dragMode: 'crop',
        cropBoxMovable: false,
        cropBoxResizable: false,
        zoomOnWheel: false,
        autoCropArea: 1,
        background: false,
        crop: function (e) {
            cropRect = e.detail;
            if (cropRect.width == 0 || cropRect.height == 0 || !activeAutoFill)
                $('#clippedData').text('');
            else {
                $('#clippedData').text("send data X: " + parseInt(cropRect.x) + " Y:" + parseInt(cropRect.y) + " W:" + parseInt(cropRect.width) + " H:" + parseInt(cropRect.height));
            }
        }
    });
    cropper_activated = true;
}
function updatePageButton(index) {
    console.log("method updatePageButton called");
    // debugger
    if (index == 0)
        $('#btnPrevPage').attr('disabled', 'disabled');
    else
        $('#btnPrevPage').removeAttr('disabled');
    if (index == (fileList.length - 1))
        $('#btnNextPage').attr('disabled', 'disabled');
    else
        $('#btnNextPage').removeAttr('disabled');
    $('#currentPage').text("Page: " + (current + 1) + " / " + fileList.length);
}
function updateCropBoard(index) {
    console.log("method updateCropBoard called");
    activeAutoFillButton(false);
    updatePageButton(index);
    var file = fileList[index];
    if (!file)
        return;
    createCropper(file);
}
function activeAutoFillButton(active) {
    console.log("method activeAutoFillButton called");
    if (active) {
        // $('#sidebar-wrapper *').css('cursor', "url('assets/img/magic-wand.cur') 0 32,url('../img/magic-wand.cur') 0 32,  default");
    } else {
        // $('#sidebar-wrapper *').css('cursor', "");
    }
    activeAutoFill = active;
}
global_data.showing_overlay = false;
function fileUpload(data) {
    console.log("method fileUpload called");
    if (uploadingFile)
        return;
    var canvas;
    if (cropper) {
        //dummy ends
//        setTimeout(() => {
//            $("#new-patient-address").val("150-100 College st. Toronto, ON M5G 1L5");
//            $("#new-patient-firstname").val("Hassaan Ahmed");
//            $("#new-patient-phone-number").val("647-906-6970");
//            $("#new-patient-dob").val("11/25/1987");
//            $("#new-patient-ohip").val("1234-567-123HA");
//        }, 2000);
//        return;
        //dummy starts
        uploadingFile = true;
        console.log("cropper form data");
        canvas = cropper.getCroppedCanvas();
        console.log(canvas);
        var imageObj = $('#_blob');

        global_data.overlay_image = canvas.toDataURL();
        $("#overlay_image").attr("src", global_data.overlay_image);
        $("#overlay_image").show("slow");
        global_data.showing_overlay = true;
        setTimeout(function () {
            global_data.showing_overlay = false;
        }, 2000);

        imageObj.attr('src', canvas.toDataURL());
        imageObj.css('width', canvas.width + 'px');
        imageObj.css('height', canvas.height + 'px');
        canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('file', blob);
            console.log("building form data");
            $.ajax('http://165.227.45.30/predict', {
//            $.ajax('http://165.227.45.30/predict_form', {
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    tmp_selector = "#anything_fake";
                    root = $("#signupForm");
                    data_points = 0;

                    data_points_captured = {};
                    data_points_captured.first_name = "";
                    data_points_captured.last_name = "";
                    data_points_captured.dob_day = "";
                    data_points_captured.dob_month = "";
                    data_points_captured.dob_year = "";
                    data_points_captured.icn = "";
                    data_points_captured.phone = {};
                    data_points_captured.phone.phone = "";
                    data_points_captured.phone.cell = "";
                    data_points_captured.phone.work = "";
                    data_points_captured.email = "";
                    data_points_captured.gender = "";
                    data_points_captured.address = "";
                    data_points_captured.success = response.success;
//                    debugger
                    if (response.success) {
                        if (response.predictions.name.hasOwnProperty('first_name')) {
                            if (response.predictions.name.first_name != "") {
                                root.find("#new-patient-firstname").val(response.predictions.name.first_name);
                                tmp_selector += ', #new-patient-firstname';
                                data_points += 1;
                                data_points_captured.first_name = response.predictions.name.first_name;
                            }
                        }
                        if (response.predictions.name.hasOwnProperty('last_name')) {
                            if (response.predictions.name.last_name != "") {
                                root.find("#new-patient-lastname").val(response.predictions.name.last_name);
                                tmp_selector += ', #new-patient-lastname';
                                data_points += 1;
                                data_points_captured.last_name = response.predictions.name.last_name;
                            }
                        }
                        if (response.predictions.DOB.hasOwnProperty('Day')) {
                            if (response.predictions.DOB.Day <= 9) {
                                response.predictions.DOB.Day = "0" + response.predictions.DOB.Day;
                            }
                            if (response.predictions.DOB.Day != "") {
                                root.find("#pat_dob_day").val(response.predictions.DOB.Day);
                                tmp_selector += ', #pat_dob_day';
                                data_points += 1;
                                data_points_captured.dob_day = response.predictions.DOB.Day;
                            }
                        }
                        if (response.predictions.DOB.hasOwnProperty('Month')) {
                            if (response.predictions.DOB.Month <= 9) {
                                response.predictions.DOB.Month = "0" + response.predictions.DOB.Month;
                            }
                            if (response.predictions.DOB.Month != "") {
                                root.find("#pat_dob_month").val(response.predictions.DOB.Month);
                                tmp_selector += ', #pat_dob_month';
                                data_points_captured.dob_month = response.predictions.DOB.Month;
                            }
                        }
                        if (response.predictions.DOB.hasOwnProperty('Year')) {
                            if (response.predictions.DOB.Year != "") {
                                root.find("#pat_dob_year").val(response.predictions.DOB.Year);
                                tmp_selector += ', #pat_dob_year';
                                data_points_captured.dob_year = response.predictions.DOB.Year;
                            }
                        }
                        if (response.predictions.hasOwnProperty('ICN')) {
                            if (response.predictions.ICN.hasOwnProperty('NO')) {
                                if (response.predictions.ICN.NO != "") {
                                    root.find("#new-patient-ohip").val(response.predictions.ICN.NO);
                                    data_points_captured.icn = response.predictions.ICN.NO;
                                    tmp_selector += ', #new-patient-ohip';
                                    data_points += 1;
                                }
                            }
                        }

                        if (response.predictions.hasOwnProperty('phone')) {
//                            if (response.predictions.phone.hasOwnProperty('phone')) {
//                                if (response.predictions.phone.phone != "") {
//                                    root.find("#patient-cell-phone").val(response.predictions.phone.phone);
//                                    data_points_captured.phone.phone = response.predictions.phone.phone;
//                                    tmp_selector += ', #patient-cell-phone';
//                                    data_points += 1;
//                                }
//                            }
                            if (response.predictions.phone.hasOwnProperty('phone')) {
                                if (response.predictions.phone.phone != "") {
                                    root.find("#patient-cell-phone").val(response.predictions.phone.phone);
                                    data_points_captured.phone.phone = response.predictions.phone.phone;
                                    tmp_selector += ', #patient-cell-phone';
                                    data_points += 1;
                                }
                            }
                            if (response.predictions.phone.hasOwnProperty('cell')) {
                                if (response.predictions.phone.cell != "") {
                                    root.find("#patient-cell-phone").val(response.predictions.phone.cell);
                                    data_points_captured.phone.cell = response.predictions.phone.cell;
                                    tmp_selector += ', #patient-cell-phone';
                                    data_points += 1;
                                }
                            }
                            if (response.predictions.phone.hasOwnProperty('home')) {
                                if (response.predictions.phone.home != "") {
                                    root.find("#patient-home-phone").val(response.predictions.phone.home);
                                    data_points_captured.phone.home = response.predictions.phone.home;
                                    tmp_selector += ', #patient-home-phone';
                                    data_points += 1;
                                }
                            }
                            if (response.predictions.phone.hasOwnProperty('work')) {
                                if (response.predictions.phone.work != "") {
                                    root.find("#patient-work-phone").val(response.predictions.phone.work);
                                    data_points_captured.phone.work = response.predictions.phone.work;
                                    tmp_selector += ', #patient-work-phone';
                                    data_points += 1;
                                }
                            }
                            if (response.predictions.phone.hasOwnProperty('business')) {
                                if (response.predictions.phone.business != "") {
                                    root.find("#patient-work-phone").val(response.predictions.phone.business);
                                    data_points_captured.phone.business = response.predictions.phone.business;
                                    tmp_selector += ', #patient-work-phone';
                                    data_points += 1;
                                }
                            }
                        }
                        if (response.predictions.hasOwnProperty('email')) {
                            if (response.predictions.email != "") {
                                root.find("#patient-email-id").val(response.predictions.email);
                                data_points_captured.email = response.predictions.email;
                                tmp_selector += ', #patient-email-id';
                                data_points += 1;
                            }
                        }
                        if (response.predictions.hasOwnProperty('gender')) {
                            gender = response.predictions.gender.toLowerCase();
                            select = "";
                            selected = false;
                            if (gender == "m" || gender == "male") {
                                select = "male";
                                selected = true;
                            } else if (gender == "f" || gender == "female") {
                                select = "female";
                                selected = true;
                            }

                            if (selected) {
                                root.find("#pat_gender").val(select);
                                data_points_captured.gender = select;
                                tmp_selector += ', #pat_gender';
                                data_points += 1;
                            }
                        }
                        if (response.predictions.hasOwnProperty('address')) {
                            if (response.predictions.address != "") {
                                root.find("#pat_geocomplete").val(response.predictions.address);
                                data_points_captured.address = response.predictions.address;
                                tmp_selector += ', #pat_geocomplete';
                                data_points += 1;
                            }
                        }

                        //mark updated animation
                        $(tmp_selector).toggleClass("updated_mode");
                        setTimeout(function () {
                            $(tmp_selector).toggleClass("updated_mode");
                        }, 3000);
                        log_data_points(data_points, global_data.efax_id, "predict");
                        // save_predict_data_points(data_points_captured);
                    }
                    btn_autofill.button('reset');
                    uploadingFile = false;
                },
                error: function (response) {
                    console.log("error");
                    console.log(response);
                    uploadingFile = false;
                },
                complete: function () {
                    console.log("completed");
                    uploadingFile = false;
                }
            });
        });
    }
}

function file_upload_triage(data) {
    console.log("method fileUpload triage called");
    if (uploadingFile) {
        error("We are already started fetching data. Please wait");
        return;
    }
    var canvas;

    if (cropper) {
        uploadingFile = true;
        console.log("cropper form data");
        canvas = cropper.getCroppedCanvas();

        console.log(canvas);
        var imageObj = $('#_blob');
        imageObj.attr('src', canvas.toDataURL());
        imageObj.css('width', canvas.width + 'px');
        imageObj.css('height', canvas.height + 'px');
        canvas.toBlob(function (blob) {
            var formData = new FormData();
            formData.append('file', blob);
            console.log("building form data");
            global_data.api_drug_test = "running";
            // $.ajax('http://159.89.127.142/drug', {
//            $.ajax('http://165.227.45.30/drug', {
            $.ajax('http://165.227.45.30:8000/extMedications', {

                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    global_data.api_drug_test = "completed";
                    tmp_selector = "#anything_fake";
                    root = $("#signupForm");
                    data_points = 0;

                    data_points_captured = {};
                    data_points_captured.disease_words = [];
                    data_points_captured.lab_tests = [];
                    data_points_captured.sign_and_synd_words = [];
                    data_points_captured.devices_and_procedures = [];
                    data_points_captured.pharmacologic_substance = [];

                    // debugger
                    if (response.success) {
                        if (response.predictions.hasOwnProperty('disease_words')) {
                            tmp = response.predictions.disease_words;
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    for (j = 0; j < text.length; j++) {
                                        disease = text[j];
                                        if (disease != "source" && disease != "source_count") {
                                            data_points += 1;
                                            add_diseases(disease);
                                            elem.concept = my_string(disease);
                                            if (Array.isArray(tmp[i][disease])) {
                                                //save multiple sentences
                                                sentences = tmp[i][disease];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.disease_words.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][disease].sentence);
                                                data_points_captured.disease_words.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (response.predictions.hasOwnProperty('lab_tests')) {
                            tmp = response.predictions.lab_tests;
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    for (j = 0; j < text.length; j++) {
                                        test = text[j];
                                        if (test != "source" && test != "source_count") {
                                            add_tests(test);
                                            data_points += 1;
                                            elem.concept = my_string(test);
                                            if (Array.isArray(tmp[i][test])) {
                                                //save multiple sentences
                                                sentences = tmp[i][test];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.lab_tests.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][test].sentence);
                                                data_points_captured.lab_tests.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (response.predictions.hasOwnProperty('sign_and_synd_words')) {
                            tmp = response.predictions.sign_and_synd_words;
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    for (j = 0; j < text.length; j++) {
                                        sign = text[j];
                                        if (sign != "source" && sign != "source_count") {
                                            add_symptoms(sign);
                                            data_points += 1;
                                            elem.concept = my_string(sign);
                                            if (Array.isArray(tmp[i][sign])) {
                                                //save multiple sentences
                                                sentences = tmp[i][sign];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.sign_and_synd_words.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][sign].sentence);
                                                data_points_captured.sign_and_synd_words.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (response.predictions.hasOwnProperty('devices')) {
                            tmp = response.predictions.devices;
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    for (j = 0; j < text.length; j++) {
                                        device = text[j];
                                        if (device != "source" && device != "source_count") {
                                            add_devices(device);
                                            data_points += 1;
                                            elem.concept = my_string(device);
                                            if (Array.isArray(tmp[i][device])) {
                                                //save multiple sentences
                                                sentences = tmp[i][device];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.devices_and_procedures.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][device].sentence);
                                                data_points_captured.devices_and_procedures.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (response.predictions.hasOwnProperty('procedures')) {
                            tmp = response.predictions.procedures;
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    for (j = 0; j < text.length; j++) {
                                        device = text[j];
                                        if (device != "source" && device != "source_count") {
                                            add_devices(device);
                                            data_points += 1;
                                            elem.concept = my_string(device);
                                            if (Array.isArray(tmp[i][device])) {
                                                //save multiple sentences
                                                sentences = tmp[i][device];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.devices_and_procedures.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][device].sentence);
                                                data_points_captured.devices_and_procedures.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (response.predictions.hasOwnProperty('pharmacologic_substance')) {
                            tmp = response.predictions.pharmacologic_substance;
                            debugger
                            if (tmp != "No Match Found" && tmp.length != 0) {
                                for (i = 0; i < tmp.length; i++) {
                                    elem = {};
                                    elem.source = my_string(tmp[i].source);

                                    text = Object.keys(tmp[i]);
                                    //response.predictions.pharmacologic_substance[0].bisphosphonate.text
                                    for (j = 0; j < text.length; j++) {
                                        medic = text[j];
                                        if (medic != "source" && medic != "source_count") {
                                            suffix = "";
                                            attrs = tmp[i][text].attributes;
                                            if (attrs.length !== 0) {
                                                suffix_array = [];
                                                for (attr_index = 0; attr_index < attrs.length; attr_index++) {
                                                    cur_attr = attrs[attr_index];
                                                    if(typeof(cur_attr.Type) !== "undefined" && typeof(cur_attr.Text) !== "undefined" ) {
                                                        suffix_array.push(cur_attr.Type.substring(0, 3) + ": " + cur_attr.Text);
                                                    }
                                                }
                                                suffix += "(" + suffix_array.join(", ");
                                            }


                                            data_points += 1;
                                            add_medications(medic + suffix);
                                            elem.concept = my_string(medic + suffix);
                                            if (Array.isArray(tmp[i][medic])) {
                                                //save multiple sentences
                                                sentences = tmp[i][medic];
                                                for (k = 0; k < sentences.length; k++) {
                                                    elem.sentence = my_string(sentences[k].sentence);
                                                    data_points_captured.pharmacologic_substance.push(elem);
                                                }
                                            } else {
                                                elem.sentence = my_string(tmp[i][medic].sentence);
                                                data_points_captured.pharmacologic_substance.push(elem);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //mark updated animation
                        $(tmp_selector).toggleClass("updated_mode");
                        setTimeout(function () {
                            $(tmp_selector).toggleClass("updated_mode");
                        }, 3000);
                        log_data_points(data_points, global_data.efax_id, "drug");
                        save_drug_data_points(data_points_captured);
                    }
                    if (global_data.api_medication_test == "completed") {
                        btn_autofill.button('reset');
                        uploadingFile = false;
                    }
                },
                error: function (response) {
                    global_data.api_drug_test = "completed";
                    console.log("error");
                    console.log(response);
                },
                complete: function () {
                    global_data.api_drug_test = "completed";
                    if (global_data.api_medication_test == "completed") {
                        btn_autofill.button('reset');
                        uploadingFile = false;
                    }
                    console.log("completed");
                }
            });


            global_data.api_medication_test = "completed";
            // $.ajax('http://159.89.127.142/medication_test', {
            //     method: 'POST',
            //     data: formData,
            //     processData: false,
            //     contentType: false,
            //     success: function (response) {
            //         global_data.api_medication_test = "completed";
            //         console.log(response);

            //         tmp_selector = "#anything_fake";
            //         root = $("#signupForm");
            //         if (response.success) {

            //             if (response.predictions.hasOwnProperty('medications')) {
            //                 tmp = response.predictions.medications;
            //                 if (tmp.hasOwnProperty('pharmacologic_substance')) {
            //                     tmp = tmp.pharmacologic_substance;
            //                     debugger
            //                     tmp = Object.keys(tmp[0]);
            //                     if (tmp != "No Match Found" && tmp.length != 0) {
            //                         for (i = 0; i < tmp.length; i++) {
            //                             if (tmp[i] != "source" && tmp[i] != "source_count") {
            //                                 add_medications(tmp[i]);
            //                             }
            //                         }
            //                     }
            //                 }
            //             }

            //             //mark updated animation
            //             $(tmp_selector).toggleClass("updated_mode");
            //             setTimeout(function () {
            //                 $(tmp_selector).toggleClass("updated_mode");
            //             }, 3000);
            //         }
            //         if (global_data.api_drug_test == "completed") {
            //             btn_autofill.button('reset');
            //             uploadingFile = false;
            //         }
            //     },
            //     error: function (response) {
            //         if (global_data.api_drug_test == "completed") {
            //             btn_autofill.button('reset');
            //             uploadingFile = false;
            //         }
            //         console.log("Error");
            //         console.log(response);
            //     },
            //     complete: function () {
            //         if (global_data.api_drug_test == "completed") {
            //             btn_autofill.button('reset');
            //             uploadingFile = false;
            //         }
            //     }
            // });
        });
    }
}

function my_string(value) {
    if (typeof (value) === "undefined" || value === null || typeof (value) != "string") {
        return "";
    } else {
        return value;
    }
}

function check_rotate_cropbox() {
//    debugger
    cropbox = cropper.getCropBoxData();
    canvasbox = {
        left: cropper.getCanvasData().left,
//        top: cropper.getCanvasData().top,
        width: cropper.getCanvasData().width,
//        height: cropper.getCanvasData().height
    };
    cropbox = {
        left: cropper.getCropBoxData().left,
//        top: cropper.getCropBoxData().top,
        width: cropper.getCropBoxData().width,
//        height: cropper.getCropBoxData().height
    }
    if (JSON.stringify(canvasbox) == JSON.stringify(cropbox)) {
        setTimeout(function () {
            a = cropper.getCanvasData();
            cropper.setCropBoxData(a);
        }, 200);
    }
}

$(document).ready(function () {

    $('#btnAutoFill').on('click', function () {
        console.log("method btnAutoFill click");
        if (cropper) {
            //start loading
            btn_autofill = $(this);
            btn_autofill.button('loading');
            fileUpload(cropper.getImageData());
        } else {
            error("Please activate cropper before autofill");
        }
    });


    $('#btnAutofillTriage').on('click', function () {
        console.log("method btnAutoFill click");

        if (cropper_activated) {
            global_data.crop_rotate = cropper.getData().rotate;
        } else {
            createCropper();
        }

//        createCropper();
        setTimeout(function () {
//            cropper.rotate(global_data.crop_rotate);
            cropper.setCropBoxData(cropper.getCanvasData());
            setTimeout(execute_file_upload, 1000);
        }, 100);
        //start loading
        btn_autofill = $(this);
        btn_autofill.button('loading');


    });

    function execute_file_upload() {
        if (!cropper || cropper == null) {
            setTimeout(execute_file_upload, 1000);
            return;
        }
        file_upload_triage(cropper.getImageData());
    }


    $('#btnPrevPage').on('click', function (evt) {
        current--;
        $(".toolbar").hide();
        if (current < 0) {
            current = 0;
            return;
        }
        if (cropper_activated) {
            global_data.crop_data = cropper.getCropBoxData();
            global_data.crop_rotate = cropper.getData().rotate;
        }
        set_load_tiff_page(current);
    });
    $('#btnNextPage').on('click', function (evt) {
        current++;
        $(".toolbar").hide();
        if (fileList.length <= current) {
            current = fileList.length - 1;
            return;
        }
        if (cropper_activated) {
            global_data.crop_data = cropper.getCropBoxData();
            global_data.crop_rotate = cropper.getData().rotate;
        }
        set_load_tiff_page(current);
    });
    $("[data-target='#eFax-modal']").on('click', function (evt) {
        init();
    });
    $('.topbar_button').on('click', function (evt) {
        if (!cropper_activated) {
            createCropper();
        }
        setTimeout(function () {
            const action = evt.currentTarget.dataset.action;
            console.log("topbar action:" + action);
            switch (action) {
                case 'rotate-left':
                    check_rotate_cropbox();
                    cropper.rotate(-90);
                    break;
                case 'rotate-right':
                    if (!cropper)
                        break;
                    check_rotate_cropbox();
                    cropper.rotate(90);
                    break;
            }
        }, 300);

    });

    $('.toolbar__button').on('click', function (evt) {
        const action = evt.currentTarget.dataset.action;
        console.log("tool action:" + action);
        switch (action) {
            case 'move':
            {
                if (!cropper)
                    break;
                drag_mode = 'move';
                activeAutoFillButton(false);
                cropper.setDragMode(action);
                break;
            }
            case 'crop':
            {
                if (!cropper)
                    break;
                drag_mode = 'crop';
                activeAutoFillButton(true);
                cropper.setDragMode(action);
                break;
            }
            case 'zoom-in':
                if (!cropper)
                    break;
                cropper.zoom(0.1);
                break;
            case 'zoom-out':
                if (!cropper)
                    break;
                cropper.zoom(-0.1);
                break;
            case 'rotate-left':
                cropper.rotate(-90);
                break;
            case 'rotate-right':
                if (!cropper)
                    break;
                cropper.rotate(90);
                break;
            case 'flip-horizontal':
                if (!cropper)
                    break;
                cropper.scaleX(-cropper.getData().scaleX || -1);
                break;
            case 'flip-vertical':
                if (!cropper)
                    break;
                cropper.scaleY(-cropper.getData().scaleY || -1);
                break;
            default:
        }
    });
});
