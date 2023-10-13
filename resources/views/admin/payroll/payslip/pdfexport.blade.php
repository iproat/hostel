<style type="text/css">
table{font-family:calibri;font-size:14px; text-align:left;}
.width20{width: 20%}
.width21{width: 21%}
.width28{width: 28%;}
.width11{width: 11%}
.width25{width: 25%}
.width100{width: 100%;text-align:left; }
.width50{width: 50%;text-align:left;}
.al-right{text-align: right;}
.al-center{text-align: center;}
.text-italic{font-style: italic;}
.text-bold{font-weight: bold;}
.wages td{text-align: right;}
.border1{border: 1px solid grey;}
.border2{border: 2px solid grey;}
.b-top0{border-top: 0px !important}
.b-collapse{border-collapse: collapse;}
.padding25{padding: 25px;}
.padding13{padding: 13px;}
.height20{height:25px;}
.border-right0{border-right:0px !important;}
.border-leftt0{border-left:0px !important;}
.left-border{border: 1px solid grey;}
.font-18{
	font-size:18px;
}
.font-14{
	font-size:14px;
}
</style>
<center>
<table   class="border2 width100"  >
	<tr><td class="al-center"><b class="font-18"> RESICO (INDIA) PRIVATE LIMITED </b><br>Survey No: 196/4B <br>
126-Mettupalayam Village Sriperumbudur Taluk<br>
Kanchipuram-631 604<br>
Phone - 044-442710739</td></td></tr>
</table>
 @php
 $dateObject = DateTime::createFromFormat('!m',$payroll->month);
$monthName = $dateObject->format('F'); // March

 @endphp
<table cellpadding="5" cellspacing="5" class="border1 width100 b-collapse" border="1">
	<tr><td class="width25"><b>Employee Name: </b></td><td class="width25">{{$employee->first_name." ".$employee->last_name}}</td><td class="width25">No of Days </td><td class="width25 al-right">{{round($payroll->total_days)}}</td></tr>
	<tr><td class="width25"><b>Designation: </b></td><td class="width25">{{$designation->designation_name}}</td><td class="width25">Payable Days</td><td class="al-right">{{$payroll->total_paying_days}}</td></tr>
	<tr><td class="width25"><b>Month & Year:</b></td><td class="width25">{{$monthName.'-'.$payroll->year}}</td><td class="width25">PF No.</td><td class="al-right">{{$payroll->pf_account_number}}</td></tr>	 
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
</table>
 


<table cellpadding="5" cellspacing="5" class="border1 width100 b-collapse"    border="1">
<tr><td class="width25 al-right border-right0 "><b>Earnings</b></td><td class="border-leftt0"></td><td class="width25 al-right border-right0"><b> Deductions</b></td><td class="border-leftt0"></td></tr>
 
</table> 
<table cellpadding="5" cellspacing="5" class="border1 width100  b-collapse" border="1">
	<tr><td class="width25">Basic </td><td class="al-right">{{round($basic)}}</td><td class="width25">Provident Fund {{round($settings->employee_pf)}}% </td><td class="width25 al-right">{{round($payroll->employee_pf)}}</td></tr>
	<tr><td class="width25">HRA</td><td class="al-right">{{round($payroll->hra_amount)}}</td><td class="width25">E.S.I.</td><td class="width25 al-right">{{round($payroll->esi_amount)}}</td></tr>
	<tr><td class="width25">Over Time</td class="al-right"><td class="al-right">{{round($payroll->ot_amount)}}  </td><td class="width25">Salary Advance</td><td class="width25 al-right">{{round($payroll->advance_deduction)}}</td></tr>
	<tr><td class="width25">Incentive </td><td class="al-right">-</td><td class="width25"> </td><td class="width25 al-right"></td></tr> 
	<tr><td class="width25">  </td><td class="al-right"> </td><td class="width25"> </td><td>-</td></tr>
	<!-- <tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr> -->
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-collapse" border="1">
	 
	<tr><td class="width25"><b>Total Earnings</b></td><td class="al-right"><b>{{round($payroll->gross_salary)}} </b></td><td class="width25"><b>Total Deduction</b></td><td td class="width25 al-right"><b> {{$payroll->esi_amount + $payroll->employee_pf + $payroll->advance_deduction }}</b></td></tr> 
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100  b-collapse" border="1">
<tr><td  class="width25 border-right0"> </td> <td class="width25 border-left0"></td><td class="width25 border-leftt0"><b>Net Salary </b></td><td class="width25 al-right" ><b>{{round($payroll->net_salary)}}</b> </td></tr> 
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0 b-collapse" border="1">
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
</table>

<table cellpadding="5" cellspacing="5" class="border1 width100 b-collapse" border="1">
	<tr><td class="width50 al-center border-right0"> Signature of the Employee: </td> <td class="width50 al-center"> Authorized signatory: </td> </tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100   b-collapse" border="1">
    <tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
	<tr><td class="width25 padding13"></td><td> </td><td class="width25"> </td><td> </td></tr>
</table>
</center>