<table>
    <thead>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">No</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Employee_No</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Employee_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Company_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Gender</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">POB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">DOB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Blood_Type</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Religion</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Personal_Email</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Tribe</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Phone_1</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Phone_1_Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Phone_2</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Phone_2_Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #cfe2f3;">Education_Level</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #cfe2f3;">Ijazah File</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #fff2cc;">Bank_Account_Number</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #fff2cc;">Bank File</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f4cccc;">NPWP_Number</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f4cccc;">NPWP File</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #ead1dc;">Identity_Number</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #ead1dc;">Identity_Expiry</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #ead1dc;">KTP File</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d2e9;">Family_Card_Number</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #d9d2e9;">KK File</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Marital_Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Family_Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Spouse_Relation</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">PTKP_Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Spouse_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Spouse_DOB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Child_Count</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_1_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_1_Relation</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_1_DOB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_2_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_2_Relation</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_2_DOB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_3_Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_3_Relation</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">child_3_DOB</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #f2f2f2;">Last_Update_Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $row)
        <tr>
            <td style="border: 1px solid #000000;">{{ $key + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $row->employee_no }}</td>
            <td style="border: 1px solid #000000;">{{ $row->employee_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->company_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->status }}</td>
            <td style="border: 1px solid #000000;">{{ $row->gender }}</td>
            <td style="border: 1px solid #000000;">{{ $row->pob }}</td>
            <td style="border: 1px solid #000000;">{{ $row->dob ? \Carbon\Carbon::parse($row->dob)->format('d-M-y') : '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $row->blood_type }}</td>
            <td style="border: 1px solid #000000;">{{ $row->religion }}</td>
            <td style="border: 1px solid #000000;">{{ $row->personal_email }}</td>
            <td style="border: 1px solid #000000;">{{ $row->tribe }}</td>
            <td style="border: 1px solid #000000;">{{ $row->phone_1 }}</td>
            <td style="border: 1px solid #000000;">{{ $row->phone_1_status }}</td>
            <td style="border: 1px solid #000000;">{{ $row->phone_2 }}</td>
            <td style="border: 1px solid #000000;">{{ $row->phone_2_status }}</td>
            <td style="border: 1px solid #000000; background-color: #cfe2f3;">{{ $row->education_level }}</td>
            <td style="border: 1px solid #000000; background-color: #cfe2f3;">{{ $row->ijazah_file }}</td>
            <td style="border: 1px solid #000000; background-color: #fff2cc;">{{ $row->bank_account_number }}</td>
            <td style="border: 1px solid #000000; background-color: #fff2cc;">{{ $row->bank_book_file }}</td>
            <td style="border: 1px solid #000000; background-color: #f4cccc;">{{ $row->npwp_number }}</td>
            <td style="border: 1px solid #000000; background-color: #f4cccc;">{{ $row->npwp_file }}</td>
            <td style="border: 1px solid #000000; background-color: #ead1dc;">{{ $row->identity_number }}</td>
            <td style="border: 1px solid #000000; background-color: #ead1dc;">{{ $row->identity_expiry }}</td>
            <td style="border: 1px solid #000000; background-color: #ead1dc;">{{ $row->ktp_file }}</td>
            <td style="border: 1px solid #000000; background-color: #d9d2e9;">{{ $row->family_card_number }}</td>
            <td style="border: 1px solid #000000; background-color: #d9d2e9;">{{ $row->family_card_file }}</td>
            <td style="border: 1px solid #000000;">{{ $row->marital_status }}</td>
            <td style="border: 1px solid #000000;">{{ $row->family_status }}</td>
            <td style="border: 1px solid #000000;">{{ $row->spouse_relation }}</td>
            <td style="border: 1px solid #000000;">{{ $row->ptkp_status }}</td>
            <td style="border: 1px solid #000000;">{{ $row->spouse_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->spouse_dob }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_count }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_1_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_1_relation }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_1_dob }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_2_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_2_relation }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_2_dob }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_3_name }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_3_relation }}</td>
            <td style="border: 1px solid #000000;">{{ $row->child_3_dob }}</td>
            <td style="border: 1px solid #000000;">{{ $row->last_update_time }}</td>
        </tr>
        @endforeach
    </tbody>
</table>