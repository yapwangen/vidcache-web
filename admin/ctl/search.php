<?php
use \Vidcache\Admin\Search;
redirect(Search::find(post('search')));
