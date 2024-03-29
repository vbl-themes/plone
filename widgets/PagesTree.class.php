<?php
/**
 * Copyright 2019 by Baltnet Communications (https://www.baltnet.ee)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class PagesTree extends WP_Widget {

	static $widget_name = "Pages Tree";
	private $walker = null;

	function __construct($walker = null) {
		$opts = array(
			"description" => "Displays sub-pages of a current page."
		);

		parent::__construct(false, __(self::$widget_name), $opts);
		$this->walker = $walker;
	}

	function __invoke() {
		register_widget($this);
	}

	function widget($args, $inst) {
		if ($inst["no_front"] && is_front_page()) return;
		if ($inst["only_pages"] && !is_page()) return;

		global $post;
		$parents = get_post_ancestors($post->ID);
		$k = count($parents);

		echo $args["before_widget"];
		echo $args["before_title"];
		echo $this->get_title($parents[$k - 1], $inst);
		echo $args["after_title"];

		echo '<ul class="pages-tree">';
		echo $this->get_pages($k > 0 ? $parents[$k - 1] : $post->ID);
		echo '</ul>';

		echo $args["after_widget"];
	}

	private function get_title($post, $inst) {
		$title = apply_filters("widget_title", $inst["title"]);
		if (empty($title)) $title = get_the_title($post);

		if (!$inst["title_link"]) return $title;
		return '<a href="' . get_permalink($post) . '">' . $title . '</a>';
	}

	private function get_pages($post_id) {
		return wp_list_pages(array(
			"sort_column" => "menu_order",
			"title_li" => "",
			"echo" => 0,
			"walker" => $this->walker,
			"child_of" => $post_id,
		));
	}

	function update($new_instance, $old_instance) {
		$inst = $old_instance;
		$inst["title"] = strip_tags($new_instance["title"]);
		$inst["title_link"] = strip_tags($new_instance["title_link"]);
		$inst["no_front"] = strip_tags($new_instance["no_front"]);
		$inst["only_pages"] = strip_tags($new_instance["only_pages"]);
		return $inst;
	}

	function form($inst) {
		?>

		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>">Title:</label>
			<input
					type="text" class="widefat"
					name="<?php echo $this->get_field_name("title"); ?>"
					id="<?php echo $this->get_field_id("title"); ?>"
					value="<?php echo $inst["title"]; ?>"
			/>
		</p>

		<p>
			<input
					type="checkbox"
					name="<?php echo $this->get_field_name("title_link"); ?>"
					id="<?php echo $this->get_field_id("title_link"); ?>"
				<?php echo $inst["title_link"] ? "checked" : ""; ?>
			/>
			<label for="<?php echo $this->get_field_id("title_link"); ?>">
				Link title to parent page
			</label>
		</p>

		<p>
			<input
					type="checkbox"
					name="<?php echo $this->get_field_name("no_front"); ?>"
					id="<?php echo $this->get_field_id("no_front"); ?>"
				<?php echo $inst["no_front"] ? "checked" : ""; ?>
			/>
			<label for="<?php echo $this->get_field_id("no_front"); ?>">
				Hide in front page?
			</label>
		</p>

		<p>
			<input
					type="checkbox"
					name="<?php echo $this->get_field_name("only_pages"); ?>"
					id="<?php echo $this->get_field_id("only_pages"); ?>"
				<?php echo $inst["only_pages"] ? "checked" : ""; ?>
			/>
			<label for="<?php echo $this->get_field_id("only_pages"); ?>">
				Show only on pages
			</label>
		</p>

		<?php
	}
}

