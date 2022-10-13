<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Visitor_Tracer
 * @subpackage Visitor_Tracer/admin/partials
 */
?>

<h3>Visitor Records</h3>
<hr>
<div id="vt_records">
    <?php
    if(isset($_GET['reset']) && $_GET['reset'] === 'vt'){
        unset($_SESSION['vt_visitor_id']);
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}visitor_tracer");
        wp_safe_redirect( admin_url('admin.php?page=visitor-tracer') );
        exit;
    }
    ?>
    <a href="<?php echo admin_url('admin.php?page=visitor-tracer&reset=vt') ?>" class="button-secondary">Reset Data</a>

    <table id="vt_records_table">
        <thead>
            <tr>
                <th></th>
                <th>Date & time</th>
                <th>Entry page</th>
                <th>Exit page</th>
                <th>Time Spent</th>
                <th>User agent</th>
                <th>Local IP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $visitors = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}visitor_tracer ORDER BY last_visit DESC");

            if($visitors){
                $keys = 1;
                foreach($visitors as $visitor){
                    ?>
                    <tr>
                        <td><?php echo $keys ?></td>
                        <td><?php echo date("F j, Y, g:i a", strtotime($visitor->last_visit)) ?></td>
                        <td>
                            <a target="_blank" href="<?php echo $visitor->entryPage ?>">
                                <?php echo $visitor->entryPage ?>
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="<?php echo $visitor->exitPage ?>">
                                <?php echo $visitor->exitPage ?>
                            </a>
                        </td>
                        <td>
                            <?php 
                                $first_visit = strtotime($visitor->first_visit);
                                $last_visit = strtotime($visitor->last_visit);
                                $timespent = gmdate('H:i:s', $last_visit - $first_visit);
                                echo $timespent;
                            ?>
                            </td>
                        <td><?php echo $visitor->user_agent ?></td>
                        <td><?php echo $visitor->local_ip ?></td>
                    </tr>
                    <?php
                    $keys++;
                }
            }else{
                echo '<tr><td colspan="6">No records found!</td></tr>';
            }
            
            ?>
        </tbody>
    </table>
</div>