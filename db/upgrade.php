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
 * Stripe enrolment plugin upgrade script.
 *
 * @package    enrol_stripepayment
 * @author     DualCube <admin@dualcube.com>
 * @copyright  2019 DualCube Team(https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_enrol_stripepayment_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025082100) {
        // Remove legacy fields that are not used by Stripe payment processing.
        $table = new xmldb_table('enrol_stripepayment');

        // Remove business field.
        $field = new xmldb_field('business');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Rename tax field to price.
        $field = new xmldb_field('tax', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'price');
        }

        // Remove option_name1 field.
        $field = new xmldb_field('option_name1');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove option_selection1_x field.
        $field = new xmldb_field('option_selection1_x');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove option_name2 field.
        $field = new xmldb_field('option_name2');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove option_selection2_x field.
        $field = new xmldb_field('option_selection2_x');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove parent_txn_id field.
        $field = new xmldb_field('parent_txn_id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Rename receiver_email to receiveremail.
        $field = new xmldb_field('receiver_email', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'receiveremail');
        }

        // Rename receiver_id to receiverid.
        $field = new xmldb_field('receiver_id', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'receiverid');
        }

        // Rename item_name to itemname.
        $field = new xmldb_field('item_name', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'itemname');
        }

        // Rename coupon_id to couponid.
        $field = new xmldb_field('coupon_id', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'couponid');
        }

        // Rename payment_status to paymentstatus.
        $field = new xmldb_field('payment_status', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'paymentstatus');
        }

        // Rename pending_reason to pendingreason.
        $field = new xmldb_field('pending_reason', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'pendingreason');
        }

        // Rename reason_code to reasoncode.
        $field = new xmldb_field('reason_code', XMLDB_TYPE_CHAR, '30', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'reasoncode');
        }

        // Rename txn_id to txnid.
        $field = new xmldb_field('txn_id', XMLDB_TYPE_CHAR, '255', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'txnid');
        }

        // Rename payment_type to paymenttype.
        $field = new xmldb_field('payment_type', XMLDB_TYPE_CHAR, '30', null, false, false);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'paymenttype');
        }
        $legacypublishable = get_config('enrol_stripepayment', 'publishablekey');
        $legacysecret = get_config('enrol_stripepayment', 'secretkey');

        // Auto-migrate legacy keys if they exist and new keys are empty.
        if (!empty($legacypublishable) && !empty($legacysecret)) {
            if (strpos($legacysecret, 'sk_test_') === 0 && strpos($legacypublishable, 'pk_test_') === 0) {
                set_config('testpublishablekey', $legacypublishable, 'enrol_stripepayment');
                set_config('testsecretkey', $legacysecret, 'enrol_stripepayment');
                set_config('stripemode', 'test', 'enrol_stripepayment');

                // Clear legacy keys after migration.
                set_config('publishablekey', '', 'enrol_stripepayment');
                set_config('secretkey', '', 'enrol_stripepayment');

            } else if (strpos($legacysecret, 'sk_live_') === 0 && strpos($legacypublishable, 'pk_live_') === 0) {
                set_config('livepublishablekey', $legacypublishable, 'enrol_stripepayment');
                set_config('livesecretkey', $legacysecret, 'enrol_stripepayment');
                set_config('stripemode', 'live', 'enrol_stripepayment');

                // Clear legacy keys after migration.
                set_config('publishablekey', '', 'enrol_stripepayment');
                set_config('secretkey', '', 'enrol_stripepayment');
            }
        }

        // Stripe savepoint reached.
        upgrade_plugin_savepoint(true, 2025082100, 'enrol', 'stripepayment');
    }

    return true;
}
