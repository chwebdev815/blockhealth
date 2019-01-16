<style>
.col-sm-6,
.col-lg-12 {
    padding-right: 15px!important;
}


#modal_signup .modal-body{
	padding-top: 0px;
	padding-bottom: 0px;
}

#modal_signup .modal-content{
	padding: 0 10px;
}

#modal_signup .modal-content .modal-header,
#modal_signup .modal-body form#form_signup fieldset{
	border: none;
	padding-bottom: 0px;
}


#modal_signup .modal-dialog {
    /*width: unset;*/
}



</style>
<div class="db-content-gutter no-left-right-padd no-bottom-padd" id="missing_upload_container" style="display:none">
    <div class="row">
        <div class="col-sm-12">
            <div class="well well-lg patient-details-well clearfix" id="headingThree">
                <div class="row form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="at-patient-details">
                            <h3 class="text-center" style="margin: 0;color: #333;font-size: 24px;">Missing referral items have been requested!</h3>
                        </div>
                    </div>
                </div>
                <div class="row form-group" style="margin-bottom: 10px;">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="at-patient-details" id="items_listing">

                        </div>
                    </div>
                </div>
                <h3 style="color: #f70d0d; text-decoration: underline;margin: 0;" id="view_upload">Upload File</h3>
            </div>
        </div>
    </div>
</div>

<div id="tracker"></div>


<!-- Sign up modal -->
<div class="modal fade" id="modal_signup" tabindex="-1" role="dialog" aria-labelledby="add-record-label" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
               <p align="center"><img src="http://dev.blockhealth.co/adi-dev/bh_fax/assets/img/signup-logo.png" width="200" /></p>
				<p style="font-size: 23px; text-align: center;"><span id="clinic_name_span">Clinic Name</span> has invited you to join BlockHealth. Signup to track your referrals in real-time</p>
            </div>
            <div class="modal-body">
                <form id="form_signup" class="form-horizontal" method="post" action="" autocomplete="off" >

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <fieldset class="fs_form">
                        <div class="form-bottom">
                            <div class="form-group">
                                <div class="form-group row">
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control required" name="fname" id="fname" placeholder="First Name">
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control required" name="lname" id="lname" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <input type="email" class="form-control valid_email required" name="email" id="email" placeholder="Email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <input type="password" class="form-control required" name="pass" id="pass" placeholder="Password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <input type="password" class="form-control required" name="confirm_pass" id="confirm_pass" placeholder="Confirm Password">
                                    </div>
                                </div> 
                                <!-- <button id="next" type="button" class="btn btn-theme signup-next btn-full">Sign Up</button> -->
                                <button id="submit" type="button" class="btn btn-theme signup-next btn-full">Sign Up as a referring Physician</button>
								<p class="marg-top-10px" align="center">I agree to the <a id="btn_show_agreement" href="javascript:void(0)">terms of service</a></p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="fs_agreement" style="display: none">
                    	<div class="form-bottom">
                            <div class="form-group">
                            	<div class="agreement_container">
                            			<h4 class="alert-heading">Terms and Services</h4>
                            			<address>
                            				This Services Agreement (“Agreement”) is entered into and effective as of the execution below (the “Effective Date”) by and between Blockhealth, inc. (“Blockhealth”) and the Partner named above (collectively, the “Parties”). 
                            			</address>
                            			<div class="list-group">
										  <a href="javascript:void(0)" class="list-group-item list-group-item-action">
										  	<strong>1.	Definitions. </strong>
										  	<br/>
											“Services” means, collectively, an application, feature, function, machine learning algorithm or other technology that Blockhealth has made available to Partner, which Blockhealth has designated as pilot, including associated offline or mobile components.
											<br/><br/>
											“Partner Data” means electronic data and information submitted by or for Partner to the Services, excluding reports, data, assessments, analyses or compilations, collected by, derived from, created by or returned by the Services, including any derivative works thereof. 
											<br/><br/>
											“Machine Learning Services” means those projects undertaken by Blockhealth as part of the Services, in which Partner shall allow Blockhealth to access and use Partner Data, for the purposes of building, analysing, reviewing, running, training, testing and improving algorithms and machine learning models.
											<br/><br/>
											“Successor Services” means any successor version of a Service or a product or service derived from the Machine Learning Services that Blockhealth may make available as a Service to Partner.   
											<br/><br/>
											“Third Party Application” means a Web-based or offline software application that is provided by Partner or a third party and which may interoperate with the Services or Successor Services, including, for example, an application that is developed by or for Partner.
											<br/><br/>
											“Users” means individuals who are authorized by Partner to use the Services, and have been supplied user identifications and passwords by Partner (or by Blockhealth at Partner's request). 
											<br/><br/>
											“Order Form” means an ordering document specifying the Services to be provided hereunder that is entered into between the Parties.
										  </a>
										  <a href="javascript:void(0)" class="list-group-item list-group-item-action">
											<strong>2.	Use of Services.</strong>
											<br/>
											2.1.	 Use of Services.  Blockhealth shall make the Services available to Partner subject to the terms of this Agreement and the Order Form. Partner shall allow only Users to access the Services, and only for the purpose(s) agreed to by the Parties.  Blockhealth will provide applicable standard support for the Services to Partner at no additional. Blockhealth will provide the Services in accordance with laws and government regulations applicable to Blockhealth’s provision of its Services to its partners and partners generally, and subject to Partner’s use of the Services in accordance with this Agreement and the applicable Order Form. For clarity, the Services contemplated herein are for evaluation and validation purposes only. The Parties shall enter into a subsequent agreement for the purchase or procurement of Successor Services.   
											<br/><br/>
											2.2.	 Protection of Partner Data. Blockhealth will only use Partner Data in providing the Services and as permitted by Partner. Blockhealth will maintains appropriate safeguards for protection of the security, confidentiality and integrity of Partner Data. Those safeguards will include, but will not be limited to, measures for preventing access, use, modification or disclosure of Partner Data by Blockhealth personnel except as outlined in this Agreement or to prevent or address service or technical problems, (b) as compelled by law, or (c) as expressly permitted in writing by Partner. Blockhealth will not, nor attempt to, use Partner Data other than in accordance with this Agreement.  
											<br/><br/>
											2.3.	 Partner Responsibilities.   Partner will (a) be responsible for Users’ compliance with this Agreement and Order Forms, (b) be responsible for the accuracy, quality and legality of Partner Data and the means by which Partner acquired Partner Data, (c) use the Services only in accordance with this Agreement and Order Forms and applicable laws and government regulations. Partner will not, nor attempt to copy, reproduce, modify, damage, disassemble, decompile, reverse engineer or create derivative works of any Service or product, or any portion thereof.   
											<br/><br/>
											2.4.	 Integration and Interoperability. The Services contain features designed to interoperate with Third-Party Applications. To use such features, Partner may be required to obtain access to such Third-Party Applications from their providers, and may be required to grant Blockhealth access to Partner’s account(s) on such Third-Party Applications. Blockhealth cannot guarantee the continued availability of such Service features, and may cease providing them without entitling Partner to any alternatives or compensation, if for example and without limitation, the provider of a Third-Party Application ceases to make the Third-Party Application available for interoperation with the corresponding Service features in a manner acceptable. If Partner chooses to use a Third-Party Application with a Service, Partner grants Blockhealth permission to allow the Third-Party Application and its provider to access Partner Data as required for the interoperation of that Third-Party Application with the Service. Blockhealth is not responsible for any disclosure, modification or deletion of Partner Data resulting from access by such Third-Party Application or its provider. Partner acknowledges and agrees that the Services may operate from infrastructure owned and operated by third parties and data may be hosted on third party platforms for the Services.   
											 
										  </a>
										  <a href="javascript:void(0)" class="list-group-item list-group-item-action">
										  	
												<strong>3.	Proprietary Rights and Confidentiality </strong>
												<br/>
												3.1.	 Grant of Rights.  Partner hereby grants to Blockhealth a limited, non-transferable, non-exclusive right and license, as well as any other rights necessary, to host, access, display, reproduce, data mine and create derivative works from the Partner Data for the purpose of providing the Services to Partner.
												<br/><br/>

												3.2.	 Ownership. Except for the limited rights granted herein, nothing in this Agreement grants a party any right, title, or interests, including any intellectual property rights, in the other party’s products, services, data or technologies. As between the Parties: (a) Partner owns (or has the right to grant to Blockhealth the right to access, use, reproduce, syndicate, host, and display) the Partner Data (including reports, assessments, analyses or compilations of Partner Data); and (b) Blockhealth retains all right, title, and interest in and to the Services and any and all improvements, enhancements, modifications, or derivative works thereto, including all intellectual property rights therein or related thereto. The foregoing also includes any and all system performance data and Machine Learning Services, including machine learning algorithms, and the results and output of such machine learning.  No jointly owned intellectual property is created under or in connection with this Agreement. Blockhealth may use any suggestions or feedback without accounting, attribution or compensation to Partner. 
												<br/><br/>

												3.3.	 Mutual Protection of Confidential Information. Information that is disclosed by one party (the “Disclosing Party”) to the other party (the “Receiving Party”) in connection with this Agreement that is identified as confidential or that would reasonably be understood to be confidential based on the nature of the information or the circumstances surrounding its disclosure, is Confidential Information of the Disclosing Party.  The Services and all information provided or disclosed to Partner relating to the Services is Confidential Information of Blockhealth and the Partner Data is Confidential Information of the Partner.  The Receiving Party shall use the same degree of care to protect such Confidential Information that it uses to protect the confidentiality of its own confidential information of like kind (but in no event less than reasonable care) (i) not to use any Confidential Information of the Disclosing Party for any purpose outside the scope of this Agreement, and (ii) except as otherwise authorized by the Disclosing Party in writing, to limit access to Confidential Information of the Disclosing Party to those of its Users, and other employees, contractors and agents who need such access for purposes consistent with this Agreement and who have signed confidentiality agreements with the Receiving Party containing protections no less stringent than those herein.  The Receiving Party may disclose Confidential Information of the Disclosing Party if it is compelled by law to do so, provided the Receiving Party gives the Disclosing Party prior notice of such compelled disclosure (to the extent legally permitted) and reasonable assistance, at the Disclosing Party's cost, if the Disclosing Party wishes to contest the disclosure. If the Receiving Party is compelled by law to disclose the Disclosing Party’s Confidential Information as part of a civil proceeding to which the Disclosing Party is a party, and the Disclosing Party is not contesting the disclosure, the Disclosing Party will reimburse the Receiving Party for its reasonable cost of compiling and providing secure access to such Confidential Information.  Confidential Information does not include any information that (i) is or becomes generally known to the public without breach of any obligation owed to the Disclosing Party, (ii) was known to the Receiving Party prior to its disclosure by the Disclosing Party without breach of any obligation owed to the Disclosing Party, (iii) is received from a third party without breach of any obligation owed to the Disclosing Party, or (iv) was independently developed by the Receiving Party.

										  </a>
										  <a href="javascript:void(0)" class="list-group-item list-group-item-action">
										  	
												<strong>4.	General Provisions</strong>
												<br/>
												4.1	Term.   This Agreement shall commence on the Effective Date and will remain in effect unless terminated in accordance with Section 4.2 (Termination). Notwithstanding the foregoing, Partner’s right to use any particular Service under this Agreement will commence on the date Blockhealth makes such Non-GA Service available to Partner based on the relevant Order Form, and will end on the earlier of (a) the parties executing a subsequent master services agreement or other agreement for the procurement of Successor Services, or (b) termination in accordance with Section 4.2 (Termination). <br/><br/>

												4.2	 Termination.  Either party may terminate this Agreement at any time without cause upon thirty (30) days’ written notice to the other. <br/><br/>

												4.3	 No Warranty. The services are provided “as-is,” exclusive of any warranty whatsoever whether express, implied, statutory or otherwise. Blockhealth disclaims all implied warranties, including without limitation any implied warranties of merchantability and fitness for a particular purpose or non-infringement, to the maximum extent permitted by applicable law. Blockhealth disclaims all liability for any harm or damages caused by any third-party hosting providers.  The Services may contain bugs or errors.  Any participation in or use of the Services is at Partner’s sole risk.  <br/><br/>

												4.4	No Damages. In no event shall the parties have any liability hereunder for any damages whatsoever, including but not limited to direct, indirect, special, incidental, punitive, or consequential damages, or damages based on lost profits, data or use, however caused and, whether in contract, tort or under any other theory of liability, whether or not a party has been advised of the possibility of such damages.<br/><br/>

												4.5	Miscellaneous.  Neither party may not assign any of its rights or obligations hereunder, whether by operation of law or otherwise, without the prior written consent of the other party.  This Agreement shall be governed exclusively by the internal laws of the Province of Ontario, without regard to its conflicts of laws rules.  There are no third-party beneficiaries under this Agreement.  This Agreement constitutes the entire agreement between the parties, and supersedes all prior and contemporaneous agreements, proposals or representations, written or oral, concerning its subject matter.  No modification, amendment, or waiver of any provision of this Agreement shall be effective unless in writing and signed by the party against whom the change is to be asserted.<br/><br/>

												4.6	Survival.  The following provisions: “Proprietary Rights and Protection of Confidential Information,” “No Warranty,” and “No Damages,” shall survive the termination of this agreement.  
										  </a>
										</div>
                            			
										<a href="javascript:void(0)" id="btn_hide_agreement">Back</a>



                            		</pre>
                            	</div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<!--modal_upload_missing_items-->
<div class="modal fade" id="modal_upload_missing_items" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Upload Missing Items </h4>
            </div>

            <div class="modal-body">
                <div class="my-info-1">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div id="success_upload" class="alert alert-success col-lg-12" style="display: none">
                                Files have been uploaded successfully
                            </div>
                            <div id="error_upload" class="alert alert-danger col-lg-12" style="display: none">
                                Error during file upload operation.
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <form id="form_upload_missing_items" action="<?php echo base_url(); ?>tracker/upload_missing_items" method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="id" value="<?php echo $this->uri->segment(3); ?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                                <div id="uploads_container">
                                    
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-12">
                                            Add Missing Files
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <input type="file" id="missing_file" name="missing_file[]" class="form-control">
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <input type="text" id="file_name" name="file_name[]" class="form-control" placeholder="Write description for file"/>
                                        </div>
                                        <div class="col-lg-2 col-sm-0">
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="upload_template hide">
                                <div class="added_upload_field row">
                                    <div class="col-lg-2 col-sm-12">
                                        <button type="button"  class="btn btn-danger remove_field pull-right">&times;</button>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <input type="file" name="missing_file[]" class="form-control">
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <input type="text" name="file_name[]" class="form-control" placeholder="Write description for file"/>
                                    </div>
                                    <div class="col-lg-2 col-sm-0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <br/>
                                <button type="button" class="btn btn-info" id="btn_add_more_files">Add more files</button>
                            </div> 
                        </div>
                        <div id="previously_uploaded_container">
                            <div class="col-lg-12">
                                <hr/>
                                <label for="new-patient-ohip">
                                    Previously uploaded files
                                </label>
                            </div>
                            <div class="col-lg-12">
                                <table id="table_missing_items" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>File Description</th>
                                            <th>Date Uploaded</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                    	<div class="alert alert-success" id="upload_success" style="display: none">
                    		Missing files uploaded successfully.
                    	</div>
                    	<div class="alert alert-danger" id="upload_error" style="display: none">
                    		Missing files failed to upload.
                    	</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_upload_missing_items" class="btn btn-theme">
                    Upload Missing Items
                </button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View modal_view_missing_item modal -->
<div class="modal fade" id="modal_view_missing_item" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <span id='record_type'></span> Missing item uploaded document </h4>
            </div>
            <div class="modal-body">
                <div id="wrap-container" class="">
                    <!-- Page Content -->
                    <div id="page-content-wrapper">
                        <div class="container-fluid">
                            <div id="pdf_view_div"></div>
                        </div>
                    </div>
                    <!-- /#page-content-wrapper -->
                </div>
            </div>
        </div>
    </div>
</div>