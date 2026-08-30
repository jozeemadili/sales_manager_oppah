<?php
$baseUrl = env('TIRA_BASE_URL', 'http://41.59.86.178:8091');

return [
    'tiraclient'										=>array(
        'clientCode'									=>env('TIRA_CLIENT_CODE', 'OAC008'),
        'clientKey'										=>env('TIRA_CLIENT_KEY', 'xRah1zzjGA7XTb'),
        'certPassword'							    	=>env('TIRA_CERT_PASSWORD', 'password'),
        'systemCode'									=>env('TIRA_SYSTEM_CODE', 'TP_INFOMATS_001'),
        'callBackUrl'									=>env('TIRA_CALLBACK_URL', 'https://d1b5-197-250-227-32.eu.ngrok.io/api/v1/tira/callbacks'),
        'certPath'										=>env('TIRA_CERT_PATH', 'Certificates/tiramisclientprivate.pfx'),
    ),
    'endpoints'											=>array(
        'MotorVerificationReq'				            =>$baseUrl.'/dispatch/api/motor/verification/v1/request',
        'MotorCoverNoteRefReq'				            =>$baseUrl.'/ecovernote/api/covernote/non-life/motor/v2/request',
        'CoverNoteVerificationReq'		                =>$baseUrl.'/ecovernote/api/covernote/verification/min/v1/request',
        'ClaimNotificationRefReq'		            	=>$baseUrl.'/eclaim/api/claim/claim-notification/v1/request',
        'ClaimIntimationReq'				        	=>$baseUrl.'/eclaim/api/claim/claim-intimation/v1/request',
        'ClaimAssessmentReq'					        =>$baseUrl.'/eclaim/api/claim/claim-assessment/v1/request',
        'DischargeVoucherReq'					        =>$baseUrl.'/eclaim/api/claim/claim-dischargevoucher/v1/request',
        'ClaimPaymentReq'						    	=>$baseUrl.'/eclaim/api/claim/claim-payment/v1/request',
        'ClaimRejectionReq'						        =>$baseUrl.'/eclaim/api/claim/claim-rejection/v1/request',
        'PolicyReq'										=>$baseUrl.'/ecovernote/api/policy/v1/request',
        'CoverNoteRefReq'                               =>$baseUrl.'/ecovernote/api/covernote/non-life/other/v2/request',
    ),
];
