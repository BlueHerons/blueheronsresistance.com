<?php
/*
Plugin Name:   GroupMe Integration
Description:   GroupMe Intregrations
Version:       0.1
Author:        John (CaptCynicism)
Author URI:    http://blueheronsresistance.com
License:       Apache License 2.0
*/

namespace BlueHerons\Wordpress\Plugin\GroupMe;

define("GROUPME_API_URL", "https://api.groupme.com/v3%stoken=%s");

class GroupMePlugin {
	public function __construct() {

	}

	public function registerAdminMenu() {
		add_menu_page("GroupMe", "GroupMe", "is_verified", __FILE__, array($this, "optionsPage"));
		add_submenu_page(__FILE__, 'Add User to Chat', 'Add to Chat', 'is_verified', __FILE__, array($this, "addUserToChatPage"));
    }

    public function addUserToChatPage() {
        $token = get_user_meta(get_current_user_id(), "groupme_access_token", true);
        $disabled = strlen($token) <= 0;
?>
    <h2>Add User to GroupMe Chat</h2>
<?php
        if ($disabled) {
?>
    <div id="bhr_groupme_status" class="error">
        <p>
            You have not connected your GroupMe account. Please do so from <a href="<?php echo get_edit_user_link();?>">your profile</a> in order to use this tool.
        </p>
    </div>
<?php
        }
        else {
?>
    <div id="bhr_groupme_status"></div>
<?php
        }
?>
    <div id="about" style="margin-top: 15px;">
        This tool will let you add a person to a GroupMe group if you are unable to do so from the app.
        <p/>
        Please note that any action you take with this tool will be reflected in GroupMe as if you did it from within the app. <strong><em>This is not anonymous</em></strong>.
    </div>
    <form id="user_group_add">
        <table>
            <tr>
                <td>
                    <label for="src_group_id">Select a group that the person is in</label>
                </td>
                <td>
                    <select name="src_group_id" disabled="disabled"/>
                        <option value="0">Select a group...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="user_id">Select the person from that group</label>
                </td>
                <td>
                    <select name="user_id" disabled="disabled">
                        <option value="0">Select a person...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tgt_group_id">Select the group to add them to</label>
                </td>
                <td>
                    <select name="tgt_group_id" disabled="disabled">
                        <option value="0">Select a group...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" class="button" value="Add to Group" disabled="disabled" />
                </td>
            </tr>
        </table>
    </form>
<?php
        add_action('admin_footer', array($this, 'addUserToChatPageJS'));
    }

    public function addUserToChatPageJS() {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var data = {
                "action": "get_groups"
            };

            $.get(ajaxurl, data, function(r) {
                r = $.parseJSON(r);
                $.each(r, function(k,v) {
                    $("select[name='src_group_id']").append($("<option/>").attr("value", v.id).text(v.name));
                });
<?php
        $token = get_user_meta(get_current_user_id(), "groupme_access_token", true);
        if (strlen($token) > 0) {
?>
                $("select[name='src_group_id']").removeAttr("disabled");
<?php
        }
?>
            });

            $("select[name='src_group_id']").change(function(e) {
                var data = {
                    "action": "get_group_members",
                    "group": $("select[name='src_group_id'] option:selected").val()
                };
                $.get(ajaxurl, data, function(r) {
                    r = $.parseJSON(r);
                    $.each(r, function(k,v) {
                        $("select[name='user_id']").append($("<option/>").attr("value", v.id).text(v.name));
                    });
                    $("select[name='user_id']").removeAttr("disabled");
                });
            });

            $("select[name='user_id']").change(function(e) {
                var data = {
                    "action": "get_groups",
                };
                $.get(ajaxurl, data, function(r) {
                    r = $.parseJSON(r);
                    $.each(r, function(k,v) {
                        $("select[name='tgt_group_id']").append($("<option/>").attr("value", v.id).text(v.name));
                    });
                    $("select[name='tgt_group_id']").removeAttr("disabled");
                });
            });

            $("select[name='tgt_group_id']").change(function(e) {
                $("input[type='submit']").removeAttr("disabled");
            });

            $("form#user_group_add").submit(function(e) {
                 var data = {
                     "action": "add_user_to_group",
                     "user": $("select[name='user_id'] option:selected").val(),
                     "name": $("select[name='user_id'] option:selected").text(),
                     "group": $("select[name='tgt_group_id'] option:selected").val()
                };
                $.get(ajaxurl, data, function(r) {
                    r = $.parseJSON(r);
                    if (r.meta.code == 202) {
                        $("#bhr_groupme_status").html("<p>Successfully added <strong>" + $("select[name='user_id'] option:selected").text() + "</strong> to <a href=\"https://app.groupme.com/chats/" + $("select[name='tgt_group_id'] option:selected").val() + "\" target=\"_blank\">" + $("select[name='tgt_group_id'] option:selected").text() + "</a></p>").addClass("updated")
                    }
                });

                e.preventDefault();
            });
        });
    </script>
<?php
    }

    public function ajaxAddUserToGroup() {
        $gid = filter_input(INPUT_GET, "group", FILTER_SANITIZE_NUMBER_INT);
        $uid = filter_input(INPUT_GET, "user", FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);

        $url = sprintf(GROUPME_API_URL, sprintf("/groups/%s/members/add?", $gid), get_user_meta(get_current_user_id(), "groupme_access_token", true));

        $data = array("nickname" => $name, "user_id" => $uid, "guid" => uniqid());
    	$obj = new \stdClass();
        $obj->members[] = $data;
        $data = json_encode($obj);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        die($response);
    }

    public function ajaxGetGroups() {
        $url = sprintf(GROUPME_API_URL, "/groups?per_page=100&", get_user_meta(get_current_user_id(), "groupme_access_token", true));

        $ch = curl_init( $url );
    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt( $ch, CURLOPT_HEADER, 0);
    	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch))->response;
        $groups = array();
        foreach ($response as $r) {
            $groups[] = array("id" => $r->group_id, "name" => $r->name);;
        }
        usort($groups, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        die(json_encode($groups));
    }

    public function ajaxGetGroupMembers() {
        $gid = filter_input(INPUT_GET, "group", FILTER_SANITIZE_NUMBER_INT);
        $url = sprintf(GROUPME_API_URL, sprintf("/groups/%s?", $gid), get_user_meta(get_current_user_id(), "groupme_access_token", true));

        $ch = curl_init( $url );
    	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt( $ch, CURLOPT_HEADER, 0);
    	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch))->response;
        $members = array();
        foreach ($response->members as $m) {
            $members[] = array("id" => $m->user_id, "name" => $m->nickname);;
        }
        usort($members, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        die(json_encode($members));
    }
}

$plugin = new GroupMePlugin();

add_action('admin_menu', array($plugin, "registerAdminMenu"));
add_action('wp_ajax_get_groups', array($plugin, "ajaxGetGroups"));
add_action('wp_ajax_get_group_members', array($plugin, "ajaxGetGroupMembers"));
add_action('wp_ajax_add_user_to_group', array($plugin, "ajaxAddUserToGroup"));
?>
