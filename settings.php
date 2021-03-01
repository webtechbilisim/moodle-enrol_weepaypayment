<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * WeePay enrolment plugin version specification.
 *
 * @package    enrol_weepaypayment
 * @copyright  2019 Dualcube Team
 * @copyright  2021 WebTech Bilişim
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {

    $settings->add(new admin_setting_heading(
        'enrol_weepaypayment_settings',
        '',
        get_string('pluginname_desc', 'enrol_weepaypayment')
    ));
	$settings->add(new admin_setting_configtext(
        'enrol_weepaypayment/merchantid',
        get_string('merchantid', 'enrol_weepaypayment'),
        get_string('merchantid_desc', 'enrol_weepaypayment'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'enrol_weepaypayment/publishablekey',
        get_string('publishablekey', 'enrol_weepaypayment'),
        get_string('publishablekey_desc', 'enrol_weepaypayment'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'enrol_weepaypayment/secretkey',
        get_string('secretkey', 'enrol_weepaypayment'),
        get_string('secretkey_desc', 'enrol_weepaypayment'),
        '',
        PARAM_TEXT
    ));

    /*$settings->add(new admin_setting_configcheckbox(
        'enrol_weepaypayment/sandboxmode',
        get_string('sandboxmode', 'enrol_weepaypayment'),
        get_string('sandboxmode_desc', 'enrol_weepaypayment'),
        0
    ));*/

    $settings->add(new admin_setting_configcheckbox(
        'enrol_weepaypayment/mailstudents',
        get_string('mailstudents', 'enrol_weepaypayment'),
        '',
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrol_weepaypayment/mailteachers',
        get_string('mailteachers', 'enrol_weepaypayment'),
        '',
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrol_weepaypayment/mailadmins',
        get_string('mailadmins', 'enrol_weepaypayment'),
        '',
        0
    ));

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    // it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect(
        'enrol_weepaypayment/expiredaction',
        get_string('expiredaction', 'enrol_weepaypayment'),
        get_string('expiredaction_help', 'enrol_weepaypayment'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES,
        $options
    ));

    // Enrol instance defaults.
    $settings->add(new admin_setting_heading(
        'enrol_weepaypayment_defaults',
        get_string('enrolinstancedefaults', 'admin'),
        get_string('enrolinstancedefaults_desc', 'admin')
    ));

    $options = array(
        ENROL_INSTANCE_ENABLED  => get_string('yes'),
        ENROL_INSTANCE_DISABLED => get_string('no')
    );
    $settings->add(new admin_setting_configselect(
        'enrol_weepaypayment/status',
        get_string('status', 'enrol_weepaypayment'),
        get_string('status_desc', 'enrol_weepaypayment'),
        ENROL_INSTANCE_DISABLED,
        $options
    ));

    $settings->add(new admin_setting_configtext(
        'enrol_weepaypayment/cost',
        get_string('cost', 'enrol_weepaypayment'),
        '',
        0,
        PARAM_FLOAT,
        4
    ));

    $weepaycurrencies = enrol_get_plugin('weepaypayment')->get_currencies();
    $settings->add(new admin_setting_configselect(
        'enrol_weepaypayment/currency',
        get_string('currency', 'enrol_weepaypayment'),
        '',
        'TRY',
        $weepaycurrencies
    ));

    $settings->add(new admin_setting_configtext(
        'enrol_weepaypayment/maxenrolled',
        get_string('maxenrolled', 'enrol_weepaypayment'),
        get_string('maxenrolled_help', 'enrol_weepaypayment'),
        0,
        PARAM_INT
    ));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect(
            'enrol_weepaypayment/roleid',
            get_string('defaultrole', 'enrol_weepaypayment'),
            get_string('defaultrole_desc', 'enrol_weepaypayment'),
            $student->id,
            $options
        ));
    }

    $settings->add(new admin_setting_configduration(
        'enrol_weepaypayment/enrolperiod',
        get_string('enrolperiod', 'enrol_weepaypayment'),
        get_string('enrolperiod_desc', 'enrol_weepaypayment'),
        0
    ));
}
