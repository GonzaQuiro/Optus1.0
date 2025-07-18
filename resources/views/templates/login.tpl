<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>{env('APP_TITLE')}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for " name="description" />
        <meta content="" name="author" />
        <link href="{asset('/fonts/css.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/font-awesome/css/font-awesome.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/simple-line-icons/simple-line-icons.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap/css/bootstrap.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/select2/css/select2.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/select2/css/select2-bootstrap.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{asset('/global/css/plugins.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/pages/css/login-3.min.css')}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{asset('/favicon.ico')}" /> </head>
    <!-- END HEAD -->

    <body class="login" style="background-color: #6C338C !important;">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="index.html">
                <img src="{asset('/global/img/logo-small.png')}" alt="" style="width: 200px;">
            </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            
            <!-- BEGIN LOGIN FORM -->
            {*            <form class="login-form">*}
            <div class="login-form" id="login">
                <h3 class="form-title" style="text-align: center;">{$l['login'][0]}</h3>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> {$l['login'][5]} </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">{$l['login'][1]}</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="{$l['login'][1]}" name="username" id="username" data-bind="textInput: UserName" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">{$l['login'][2]}</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="{$l['login'][2]}" name="password" id="password" data-bind="textInput: Password" /> </div>
                </div>
                <div class="form-actions" style="text-align: center;">
                    <button type="button" id="btnSubmit" class="btn green" data-bind="click: Login" style="width: 50%; margin-bottom: 3px;"> {$l['login'][4]} </button>
                    <button type="button" id="btnLoginAD" class="btn" data-bind="click: LoginAD" style="width: 50%;"> {$l['login'][6]} </button>
                </div>
                <div class="forget-password">
                    <h4>{$l['login_forgot'][0]}</h4>
                    <p> {$l['login_forgot'][1]}
                        <a href="#" onclick="forgot()" id="forget-password">{$l['login_forgot'][2]}</a> {$l['login_forgot'][3]}
                    </p>
                </div>
                <div class="create-account">
                    <p> {$l['login_new'][0]}&nbsp;
                        <a href="javascript:;" id="register-btn"> {$l['login_new'][1]} </a>
                    </p>
                </div>
            </div>
            <!-- END LOGIN FORM -->
            <!-- BEGIN FORGOT PASSWORD FORM -->
            <div class="forget-form" id="forget">
                <h3>{$l['login_forgot'][0]}</h3>
                <p> {$l['login_forgot'][4]} </p>
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="{$l['login_forgot'][5]}" name="email" id="email" /> </div>
                </div>
                <div class="form-actions">
                    <button type="button" id="back-btn" class="btn grey-salsa btn-outline" onclick="login()"> {$l['login_forgot'][6]} </button>
                    <button type="button" class="btn green pull-right" onclick="sendRecoverEmail()"> {$l['login_forgot'][7]} </button>
                </div>
            </div>
            <!-- END FORGOT PASSWORD FORM -->
            <!-- BEGIN REGISTRATION FORM -->
            <form class="register-form" action="index.html" method="post">
                <h3>Sign Up</h3>
                <p> Enter your personal details below: </p>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Full Name</label>
                    <div class="input-icon">
                        <i class="fa fa-font"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="Full Name" name="fullname" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Email</label>
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="Email" name="email" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Address</label>
                    <div class="input-icon">
                        <i class="fa fa-check"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="Address" name="address" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">City/Town</label>
                    <div class="input-icon">
                        <i class="fa fa-location-arrow"></i>
                        <input class="form-control placeholder-no-fix" type="text" placeholder="City/Town" name="city" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Country</label>
                    <select name="country" id="country_list" class="select2 form-control">
                        <option value=""></option>
                        <option value="AF">Afghanistan</option>
                        <option value="AL">Albania</option>
                        <option value="DZ">Algeria</option>
                        <option value="AS">American Samoa</option>
                        <option value="AD">Andorra</option>
                        <option value="AO">Angola</option>
                        <option value="AI">Anguilla</option>
                        <option value="AR">Argentina</option>
                        <option value="AM">Armenia</option>
                        <option value="AW">Aruba</option>
                        <option value="AU">Australia</option>
                        <option value="AT">Austria</option>
                        <option value="AZ">Azerbaijan</option>
                        <option value="BS">Bahamas</option>
                        <option value="BH">Bahrain</option>
                        <option value="BD">Bangladesh</option>
                        <option value="BB">Barbados</option>
                        <option value="BY">Belarus</option>
                        <option value="BE">Belgium</option>
                        <option value="BZ">Belize</option>
                        <option value="BJ">Benin</option>
                        <option value="BM">Bermuda</option>
                        <option value="BT">Bhutan</option>
                        <option value="BO">Bolivia</option>
                        <option value="BA">Bosnia and Herzegowina</option>
                        <option value="BW">Botswana</option>
                        <option value="BV">Bouvet Island</option>
                        <option value="BR">Brazil</option>
                        <option value="IO">British Indian Ocean Territory</option>
                        <option value="BN">Brunei Darussalam</option>
                        <option value="BG">Bulgaria</option>
                        <option value="BF">Burkina Faso</option>
                        <option value="BI">Burundi</option>
                        <option value="KH">Cambodia</option>
                        <option value="CM">Cameroon</option>
                        <option value="CA">Canada</option>
                        <option value="CV">Cape Verde</option>
                        <option value="KY">Cayman Islands</option>
                        <option value="CF">Central African Republic</option>
                        <option value="TD">Chad</option>
                        <option value="CL">Chile</option>
                        <option value="CN">China</option>
                        <option value="CX">Christmas Island</option>
                        <option value="CC">Cocos (Keeling) Islands</option>
                        <option value="CO">Colombia</option>
                        <option value="KM">Comoros</option>
                        <option value="CG">Congo</option>
                        <option value="CD">Congo, the Democratic Republic of the</option>
                        <option value="CK">Cook Islands</option>
                        <option value="CR">Costa Rica</option>
                        <option value="CI">Cote d'Ivoire</option>
                        <option value="HR">Croatia (Hrvatska)</option>
                        <option value="CU">Cuba</option>
                        <option value="CY">Cyprus</option>
                        <option value="CZ">Czech Republic</option>
                        <option value="DK">Denmark</option>
                        <option value="DJ">Djibouti</option>
                        <option value="DM">Dominica</option>
                        <option value="DO">Dominican Republic</option>
                        <option value="EC">Ecuador</option>
                        <option value="EG">Egypt</option>
                        <option value="SV">El Salvador</option>
                        <option value="GQ">Equatorial Guinea</option>
                        <option value="ER">Eritrea</option>
                        <option value="EE">Estonia</option>
                        <option value="ET">Ethiopia</option>
                        <option value="FK">Falkland Islands (Malvinas)</option>
                        <option value="FO">Faroe Islands</option>
                        <option value="FJ">Fiji</option>
                        <option value="FI">Finland</option>
                        <option value="FR">France</option>
                        <option value="GF">French Guiana</option>
                        <option value="PF">French Polynesia</option>
                        <option value="TF">French Southern Territories</option>
                        <option value="GA">Gabon</option>
                        <option value="GM">Gambia</option>
                        <option value="GE">Georgia</option>
                        <option value="DE">Germany</option>
                        <option value="GH">Ghana</option>
                        <option value="GI">Gibraltar</option>
                        <option value="GR">Greece</option>
                        <option value="GL">Greenland</option>
                        <option value="GD">Grenada</option>
                        <option value="GP">Guadeloupe</option>
                        <option value="GU">Guam</option>
                        <option value="GT">Guatemala</option>
                        <option value="GN">Guinea</option>
                        <option value="GW">Guinea-Bissau</option>
                        <option value="GY">Guyana</option>
                        <option value="HT">Haiti</option>
                        <option value="HM">Heard and Mc Donald Islands</option>
                        <option value="VA">Holy See (Vatican City State)</option>
                        <option value="HN">Honduras</option>
                        <option value="HK">Hong Kong</option>
                        <option value="HU">Hungary</option>
                        <option value="IS">Iceland</option>
                        <option value="IN">India</option>
                        <option value="ID">Indonesia</option>
                        <option value="IR">Iran (Islamic Republic of)</option>
                        <option value="IQ">Iraq</option>
                        <option value="IE">Ireland</option>
                        <option value="IL">Israel</option>
                        <option value="IT">Italy</option>
                        <option value="JM">Jamaica</option>
                        <option value="JP">Japan</option>
                        <option value="JO">Jordan</option>
                        <option value="KZ">Kazakhstan</option>
                        <option value="KE">Kenya</option>
                        <option value="KI">Kiribati</option>
                        <option value="KP">Korea, Democratic People's Republic of</option>
                        <option value="KR">Korea, Republic of</option>
                        <option value="KW">Kuwait</option>
                        <option value="KG">Kyrgyzstan</option>
                        <option value="LA">Lao People's Democratic Republic</option>
                        <option value="LV">Latvia</option>
                        <option value="LB">Lebanon</option>
                        <option value="LS">Lesotho</option>
                        <option value="LR">Liberia</option>
                        <option value="LY">Libyan Arab Jamahiriya</option>
                        <option value="LI">Liechtenstein</option>
                        <option value="LT">Lithuania</option>
                        <option value="LU">Luxembourg</option>
                        <option value="MO">Macau</option>
                        <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                        <option value="MG">Madagascar</option>
                        <option value="MW">Malawi</option>
                        <option value="MY">Malaysia</option>
                        <option value="MV">Maldives</option>
                        <option value="ML">Mali</option>
                        <option value="MT">Malta</option>
                        <option value="MH">Marshall Islands</option>
                        <option value="MQ">Martinique</option>
                        <option value="MR">Mauritania</option>
                        <option value="MU">Mauritius</option>
                        <option value="YT">Mayotte</option>
                        <option value="MX">Mexico</option>
                        <option value="FM">Micronesia, Federated States of</option>
                        <option value="MD">Moldova, Republic of</option>
                        <option value="MC">Monaco</option>
                        <option value="MN">Mongolia</option>
                        <option value="MS">Montserrat</option>
                        <option value="MA">Morocco</option>
                        <option value="MZ">Mozambique</option>
                        <option value="MM">Myanmar</option>
                        <option value="NA">Namibia</option>
                        <option value="NR">Nauru</option>
                        <option value="NP">Nepal</option>
                        <option value="NL">Netherlands</option>
                        <option value="AN">Netherlands Antilles</option>
                        <option value="NC">New Caledonia</option>
                        <option value="NZ">New Zealand</option>
                        <option value="NI">Nicaragua</option>
                        <option value="NE">Niger</option>
                        <option value="NG">Nigeria</option>
                        <option value="NU">Niue</option>
                        <option value="NF">Norfolk Island</option>
                        <option value="MP">Northern Mariana Islands</option>
                        <option value="NO">Norway</option>
                        <option value="OM">Oman</option>
                        <option value="PK">Pakistan</option>
                        <option value="PW">Palau</option>
                        <option value="PA">Panama</option>
                        <option value="PG">Papua New Guinea</option>
                        <option value="PY">Paraguay</option>
                        <option value="PE">Peru</option>
                        <option value="PH">Philippines</option>
                        <option value="PN">Pitcairn</option>
                        <option value="PL">Poland</option>
                        <option value="PT">Portugal</option>
                        <option value="PR">Puerto Rico</option>
                        <option value="QA">Qatar</option>
                        <option value="RE">Reunion</option>
                        <option value="RO">Romania</option>
                        <option value="RU">Russian Federation</option>
                        <option value="RW">Rwanda</option>
                        <option value="KN">Saint Kitts and Nevis</option>
                        <option value="LC">Saint LUCIA</option>
                        <option value="VC">Saint Vincent and the Grenadines</option>
                        <option value="WS">Samoa</option>
                        <option value="SM">San Marino</option>
                        <option value="ST">Sao Tome and Principe</option>
                        <option value="SA">Saudi Arabia</option>
                        <option value="SN">Senegal</option>
                        <option value="SC">Seychelles</option>
                        <option value="SL">Sierra Leone</option>
                        <option value="SG">Singapore</option>
                        <option value="SK">Slovakia (Slovak Republic)</option>
                        <option value="SI">Slovenia</option>
                        <option value="SB">Solomon Islands</option>
                        <option value="SO">Somalia</option>
                        <option value="ZA">South Africa</option>
                        <option value="GS">South Georgia and the South Sandwich Islands</option>
                        <option value="ES">Spain</option>
                        <option value="LK">Sri Lanka</option>
                        <option value="SH">St. Helena</option>
                        <option value="PM">St. Pierre and Miquelon</option>
                        <option value="SD">Sudan</option>
                        <option value="SR">Suriname</option>
                        <option value="SJ">Svalbard and Jan Mayen Islands</option>
                        <option value="SZ">Swaziland</option>
                        <option value="SE">Sweden</option>
                        <option value="CH">Switzerland</option>
                        <option value="SY">Syrian Arab Republic</option>
                        <option value="TW">Taiwan, Province of China</option>
                        <option value="TJ">Tajikistan</option>
                        <option value="TZ">Tanzania, United Republic of</option>
                        <option value="TH">Thailand</option>
                        <option value="TG">Togo</option>
                        <option value="TK">Tokelau</option>
                        <option value="TO">Tonga</option>
                        <option value="TT">Trinidad and Tobago</option>
                        <option value="TN">Tunisia</option>
                        <option value="TR">Turkey</option>
                        <option value="TM">Turkmenistan</option>
                        <option value="TC">Turks and Caicos Islands</option>
                        <option value="TV">Tuvalu</option>
                        <option value="UG">Uganda</option>
                        <option value="UA">Ukraine</option>
                        <option value="AE">United Arab Emirates</option>
                        <option value="GB">United Kingdom</option>
                        <option value="US">United States</option>
                        <option value="UM">United States Minor Outlying Islands</option>
                        <option value="UY">Uruguay</option>
                        <option value="UZ">Uzbekistan</option>
                        <option value="VU">Vanuatu</option>
                        <option value="VE">Venezuela</option>
                        <option value="VN">Viet Nam</option>
                        <option value="VG">Virgin Islands (British)</option>
                        <option value="VI">Virgin Islands (U.S.)</option>
                        <option value="WF">Wallis and Futuna Islands</option>
                        <option value="EH">Western Sahara</option>
                        <option value="YE">Yemen</option>
                        <option value="ZM">Zambia</option>
                        <option value="ZW">Zimbabwe</option>
                    </select>
                </div>
                <p> Enter your account details below: </p>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Username</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="register_password" placeholder="Password" name="password" /> </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                    <div class="controls">
                        <div class="input-icon">
                            <i class="fa fa-check"></i>
                            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="rpassword" /> </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="mt-checkbox mt-checkbox-outline">
                        <input type="checkbox" name="tnc" /> I agree to the
                        <a href="javascript:;">Terms of Service </a> &
                        <a href="javascript:;">Privacy Policy </a>
                        <span></span>
                    </label>
                    <div id="register_tnc_error"> </div>
                </div>
                <div class="form-actions">
                    <button id="register-back-btn" type="button" class="btn grey-salsa btn-outline"> Back </button>
                    <button type="submit" id="register-submit-btn" class="btn green pull-right"> Sign Up </button>
                </div>
            </form>
            <!-- END REGISTRATION FORM -->
        </div>
        <br>
        <!-- END LOGIN -->
        <!--[if lt IE 9]>
<script src="{asset('/global/plugins/respond.min.js')}"></script>
<script src="{asset('/global/plugins/excanvas.min.js')}"></script> 
<script src="{asset('/global/plugins/ie8.fix.min.js')}"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{asset('/global/plugins/jquery.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap/js/bootstrap.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/js.cookie.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/jquery.blockui.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.min.js')}" type="text/javascript"></script>
        <script src="{asset('/pages/scripts/ui-sweetalert.min.js')}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{asset('/global/plugins/select2/js/select2.full.min.js')}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script type="text/javascript">
        var HOST = '{env('APP_SITE_URL')}';
    </script>
    <script src="{asset('/global/scripts/app.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.validation/knockout.validation.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.validation/localization/es-ES.js')}" type="text/javascript"></script>'
    <script src="{asset('/global/scripts/knockout.validation/localization/en-US.js')}" type="text/javascript"></script>'    
    <script src="{asset('/js/services.js')}" type="text/javascript"></script>
    <script src="{asset('/js/app.js')}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <script>
        AppOptus.Bind(new UserLogin());
        {literal}
            $(document).ready(function () {
                $("#username").focus();
                $("#username, #password").keypress(function (event) {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if (parseInt(keycode) === 13) {
                        $("#btnSubmit").click();
                    }
                });
            });
        {/literal}

        function forgot(){
            $("#login").hide();
            $("#forget").show();
        }

        function login(){
            $("#forget").hide();
            $("#login").show();
        }

        function sendRecoverEmail(){
            var email = $('#email').val();
            console.log(email);
            swal({
                title: '¿Desea recuperar la contraseña del usuario '+ email +' ? ',
                text: 'Se enviará un correo electrónico con el link para recuperar su contraseña.',
                type: 'info',
                closeOnClickOutside: false,
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonText: 'Aceptar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonText: 'Cancelar',
                cancelButtonClass: 'btn btn-default'
            }, function(result) {
                if (result) {
                    $.blockUI();
                    var url = '/send';
                    Services.Post(url, {
                            email
                        },
                        (response) => {
                            swal.close();
                            $.unblockUI();
                            setTimeout(function() {
                                if (response.success) {
                                    swal({
                                        title: 'Hecho',
                                        text: response.message,
                                        type: 'success',
                                        closeOnClickOutside: false,
                                        closeOnConfirm: true,
                                        confirmButtonText: 'Aceptar',
                                        confirmButtonClass: 'btn btn-success'
                                    }, function(result) {
                                        location.reload();
                                    });
                                } else {
                                    swal('Error', response.message, 'error');
                                }
                            }, 500);
                        },
                        (error) => {
                            swal.close();
                            $.unblockUI();
                            setTimeout(function() {
                                swal('Error', error.message, 'error');
                            }, 500);
                        },
                        null,
                        null
                    );
                }
            });
        }
    </script>
</body>

</html>