<style type="text/css">
table{font-family:calibri;}
.width20{width: 20%}
.width21{width: 21%}
.width28{width: 28%}
.width11{width: 11%}
.width100{width: 100%}
.al-right{text-align: right;}
.al-center{text-align: center;}
.text-italic{font-style: italic;}
.text-bold{font-weight: bold;}
.wages td{text-align: right;}
.border1{border: 1px solid grey;}
.b-top0{border-top: 0px !important}
.b-collapse{border-collapse: collapse;}
.padding25{padding: 25px;}
</style>
<table cellpadding="5" cellspacing="5" class="border1 width100">
	<tr><td class="al-center">FORM NO.XXVIII</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="al-center">(See Rule 78 (1) (b) of Tamil Nadu Contract Labour (Regulation and Abolition) Rules, 1975)</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="al-center">WAGE SLIP for the month of DEC 2022</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0 b-collapse" border="1">
	<tr><td class="width28">Contractor Name : </td><td>{{$employee->first_name." ".$employee->last_name}}</td><td class="width21">Total payable Days</td><td class="width11">78</td></tr>
	<tr><td>Employee ID </td><td>{{$employee->finger_id}}</td><td>OT Hours</td><td></td></tr>
	<tr><td>Name of the Employee</td><td>{{$employee->first_name." ".$employee->last_name}}</td><td>UAN No.</td><td></td></tr>
	<tr><td>Father's / Husband's Name</td><td>{{$employee->father_name}}</td><td>ESIC No.</td><td></td></tr>
	<tr><td>Date of Joining</td><td>{{ ($employee->date_of_joining !="0000-00-00") ? DATE('d-m-Y',strtotime($employee->date_of_joining)) : "" }}</td><td></td><td></td></tr>
	<tr><td>Designation</td><td>{{ $employee->designation->designation_name }}</td><td></td><td></td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0 b-collapse" border="1">
	<tr><td class="width28 al-center">Wages Earned </td><td class="al-center">Rs</td><td class="al-center">P</td><td class="width21 al-center">Deduction</td><td class="al-center">Rs</td><td class="al-center">P</td></tr>
	<tr class="wages"><td>Basic </td><td>789898</td><td></td><td>EPF</td><td></td><td></td></tr>
	<tr class="wages"><td>D.A </td><td>789</td><td></td><td>ESI</td><td></td><td></td></tr>
	<tr class="wages"><td>HRA </td><td></td><td></td><td>Other Deduction</td><td></td><td></td></tr>
	<tr class="wages"><td>O.T Wages </td><td></td><td></td><td>Canteen</td><td></td><td></td></tr>
	<tr class="wages"><td>Leave Wages </td><td></td><td></td><td>LWF</td><td></td><td></td></tr>
	<tr class="wages"><td>Other Allowance </td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td class="al-right text-italic text-bold">Gross Wages </td><td class="al-right"></td><td></td><td class="al-right width21 text-italic text-bold">Total Deduction</td><td class="al-right"></td><td></td></tr>
	
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="padding25 al-center"></td><td class="al-center"></td></tr>
	<tr><td class="al-center">Signature of the Employers / manager <br>or any other Authorised person</td><td class="al-center">Signature of <br>Thumb Impression of the labour</td></tr>
</table>


<pagebreak>


<table cellpadding="5" cellspacing="5" class="border1 width100">
	<tr><td class="al-center">FORM NO.XXVIII</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="al-center">(See Rule 78 (1) (b) of Tamil Nadu Contract Labour (Regulation and Abolition) Rules, 1975)</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="al-center">WAGE SLIP for the month of DEC 2022</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0 b-collapse" border="1">
	<tr><td class="width28">Contractor Name : </td><td></td><td class="width21">Total payable Days</td><td class="width20">78</td></tr>
	<tr><td>Employee ID </td><td></td><td>OT Hours</td><td></td></tr>
	<tr><td>Name of the Labour</td><td></td><td>UAN No.</td><td></td></tr>
	<tr><td>Father's / Husband's Name</td><td></td><td>ESIC No.</td><td></td></tr>
	<tr><td>Date of Joining</td><td></td><td></td><td></td></tr>
	<tr><td>Designation</td><td></td><td>Wages Period</td><td>From : 20-08-2022 <br> To &nbsp;&nbsp;&nbsp;&nbsp;: 25-08-2022</td></tr>
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0 b-collapse" border="1">
	<tr><td class="width28 al-center">Wages Earned </td><td class="al-center">Rs</td><td class="al-center">P</td><td class="width21 al-center">Deduction</td><td class="al-center">Rs</td><td class="al-center">P</td></tr>
	<tr class="wages"><td>Basic </td><td>789898</td><td></td><td>EPF</td><td></td><td></td></tr>
	<tr class="wages"><td>D.A </td><td>789</td><td></td><td>ESI</td><td></td><td></td></tr>
	<tr class="wages"><td>HRA </td><td></td><td></td><td>Other Deduction</td><td></td><td></td></tr>
	<tr class="wages"><td>O.T Wages </td><td></td><td></td><td>Canteen</td><td></td><td></td></tr>
	<tr class="wages"><td>Leave Wages </td><td></td><td></td><td>LWF</td><td></td><td></td></tr>
	<tr class="wages"><td>Other Allowance </td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td class="al-right text-italic text-bold">Gross Wages </td><td class="al-right"></td><td></td><td class="al-right width21 text-italic text-bold">Total Deduction</td><td class="al-right"></td><td></td></tr>
	
</table>
<table cellpadding="5" cellspacing="5" class="border1 width100 b-top0">
	<tr><td class="padding25 al-center"></td><td class="al-center"></td></tr>
	<tr><td class="al-center">Signature of the Employers / manager <br>or any other Authorised person</td><td class="al-center">Signature of <br>Thumb Impression of the labour</td></tr>
</table>