$(document).ready(function () {

    $('#registrantContact').change(function(e){
        var contact_val = $(this).val();

        if (contact_val == '0') {
            $('#new_registrant_contact').show();
        } else {
            $('#new_registrant_contact').hide();
        }
    });

    $('#administrativeContact').change(function(e){
        var contact_val = $(this).val();

        if (contact_val == '0') {
            $('#new_administrative_contact').show();
        } else {
            $('#new_administrative_contact').hide();
        }
    });

    $('#technicalContact').change(function(e){
        var contact_val = $(this).val();

        if (contact_val == '0') {
            $('#new_technical_contact').show();
        } else {
            $('#new_technical_contact').hide();
        }
    });

    $('#billingContact').change(function(e){
        var contact_val = $(this).val();

        if (contact_val == '0') {
            $('#new_billing_contact').show();
        } else {
            $('#new_billing_contact').hide();
        }
    });

    $('#cloneRegistrantContacts').change(function(){
        if ($(this).is(':checked')) {

            var reg = $('#containerRegistrantContacts');
            
            // Get registrant fields
            var reg_title = reg.find('[name="Registrant[title]"]').val();
            var reg_first_name = reg.find('[name="Registrant[first_name]"]').val();
            var reg_last_name = reg.find('[name="Registrant[last_name]"]').val();
            var reg_company = reg.find('[name="Registrant[company]"]').val();
            var reg_job_title = reg.find('[name="Registrant[job_title]"]').val();
            var reg_email = reg.find('[name="Registrant[email]"]').val();
            var reg_address1 = reg.find('[name="Registrant[address1]"]').val();
            var reg_address2 = reg.find('[name="Registrant[address2]"]').val();
            var reg_address3 = reg.find('[name="Registrant[address3]"]').val();
            var reg_city = reg.find('[name="Registrant[city]"]').val();
            var reg_state = reg.find('[name="Registrant[state]"]').val();
            var reg_postcode = reg.find('[name="Registrant[postcode]"]').val();
            var reg_country = reg.find('[name="Registrant[country]"]').val();
            var reg_phone_cc = reg.find('[name="Registrant[phone_cc]"]').val();
            var reg_phone = reg.find('[name="Registrant[phone]"]').val();
            var reg_fax_cc = reg.find('[name="Registrant[fax_cc]"]').val();
            var reg_fax = reg.find('[name="Registrant[fax]"]').val();

            

            // Set admin contacts
            var admin = $('#containerAdministrativeContacts');
            admin.find('[name="Administrative[title]"]').val(reg_title);
            admin.find('[name="Administrative[first_name]"]').val(reg_first_name);
            admin.find('[name="Administrative[last_name]"]').val(reg_last_name);
            admin.find('[name="Administrative[company]"]').val(reg_company);
            admin.find('[name="Administrative[job_title]"]').val(reg_job_title);
            admin.find('[name="Administrative[email]"]').val(reg_email);
            admin.find('[name="Administrative[address1]"]').val(reg_address1);
            admin.find('[name="Administrative[address2]"]').val(reg_address2);
            admin.find('[name="Administrative[address3]"]').val(reg_address3);
            admin.find('[name="Administrative[city]"]').val(reg_city);
            admin.find('[name="Administrative[state]"]').val(reg_state);
            admin.find('[name="Administrative[postcode]"]').val(reg_postcode);
            admin.find('[name="Administrative[country]"]').val(reg_country);
            admin.find('[name="Administrative[phone_cc]"]').val(reg_phone_cc);
            admin.find('[name="Administrative[phone]"]').val(reg_phone);
            admin.find('[name="Administrative[fax_cc]"]').val(reg_fax_cc);
            admin.find('[name="Administrative[fax]"]').val(reg_fax);

            // Set tech contacts
            var tech = $('#containerTechnicalContacts');
            tech.find('[name="Technical[title]"]').val(reg_title);
            tech.find('[name="Technical[first_name]"]').val(reg_first_name);
            tech.find('[name="Technical[last_name]"]').val(reg_last_name);
            tech.find('[name="Technical[company]"]').val(reg_company);
            tech.find('[name="Technical[job_title]"]').val(reg_job_title);
            tech.find('[name="Technical[email]"]').val(reg_email);
            tech.find('[name="Technical[address1]"]').val(reg_address1);
            tech.find('[name="Technical[address2]"]').val(reg_address2);
            tech.find('[name="Technical[address3]"]').val(reg_address3);
            tech.find('[name="Technical[city]"]').val(reg_city);
            tech.find('[name="Technical[state]"]').val(reg_state);
            tech.find('[name="Technical[postcode]"]').val(reg_postcode);
            tech.find('[name="Technical[country]"]').val(reg_country);
            tech.find('[name="Technical[phone_cc]"]').val(reg_phone_cc);
            tech.find('[name="Technical[phone]"]').val(reg_phone);
            tech.find('[name="Technical[fax_cc]"]').val(reg_fax_cc);
            tech.find('[name="Technical[fax]"]').val(reg_fax);

            // Set tech contacts
            var bill = $('#containerBillingContacts');
            bill.find('[name="Billing[title]"]').val(reg_title);
            bill.find('[name="Billing[first_name]"]').val(reg_first_name);
            bill.find('[name="Billing[last_name]"]').val(reg_last_name);
            bill.find('[name="Billing[company]"]').val(reg_company);
            bill.find('[name="Billing[job_title]"]').val(reg_job_title);
            bill.find('[name="Billing[email]"]').val(reg_email);
            bill.find('[name="Billing[address1]"]').val(reg_address1);
            bill.find('[name="Billing[address2]"]').val(reg_address2);
            bill.find('[name="Billing[address3]"]').val(reg_address3);
            bill.find('[name="Billing[city]"]').val(reg_city);
            bill.find('[name="Billing[state]"]').val(reg_state);
            bill.find('[name="Billing[postcode]"]').val(reg_postcode);
            bill.find('[name="Billing[country]"]').val(reg_country);
            bill.find('[name="Billing[phone_cc]"]').val(reg_phone_cc);
            bill.find('[name="Billing[phone]"]').val(reg_phone);
            bill.find('[name="Billing[fax_cc]"]').val(reg_fax_cc);
            bill.find('[name="Billing[fax]"]').val(reg_fax);

        }
    });


});