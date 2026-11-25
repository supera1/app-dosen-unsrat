<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
    }

    public function index()
    {
        $keyword  = $this->input->get('q');
        $keyword  = is_string($keyword) ? trim($keyword) : '';
        $searched = strlen($keyword) >= 2;

        $per_page = 12;
        $total    = $searched ? $this->Staff_model->count_search($keyword) : 0;

        $page = $this->input->get('page');
        $page = is_numeric($page) ? (int) $page : 1;
        $page = max(1, min($page, max(1, (int) ceil($total / $per_page))));

        $data['title']    = ($keyword !== '' ? 'Pencarian: ' . $keyword : 'Pencarian Staf') . ' - UNSRAT';
        $data['keyword']  = $keyword;
        $data['searched'] = $searched;
        $data['results']  = $searched
            ? $this->Staff_model->search($keyword, $per_page, ($page - 1) * $per_page)
            : [];
        $data['total']    = $total;
        $data['page']     = $page;
        $data['per_page'] = $per_page;

        $data['pagination'] = '';
        if ($searched && $total > $per_page) {
            $this->load->library('pagination');
            $this->pagination->initialize([
                'base_url'             => base_url('search'),
                'total_rows'          => $total,
                'per_page'            => $per_page,
                'page_query_string'   => TRUE,
                'query_string_segment' => 'page',
                'reuse_query_string'  => TRUE,
                'use_page_numbers'    => TRUE,
                'num_links'           => 2,
                'first_link'          => '&laquo;',
                'last_link'           => '&raquo;',
                'full_tag_open'       => '<div class="pagination">',
                'full_tag_close'      => '</div>',
                'cur_tag_open'        => '<span class="current">',
                'cur_tag_close'       => '</span>',
                'attributes'          => ['class' => 'page-link'],
            ]);
            $data['pagination'] = $this->pagination->create_links();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('search/index', $data);
        $this->load->view('templates/footer');
    }

    public function suggest()
    {
        $keyword = trim($this->input->get('q') ?: '');
        if (strlen($keyword) < 2) {
            $this->output->set_content_type('application/json');
            echo json_encode([]);
            return;
        }

        $results = $this->Staff_model->search($keyword);
        $out = [];
        foreach (array_slice($results, 0, 8) as $s) {
            $out[] = [
                'name'            => $s->full_title_name ?: $s->name,
                'slug'            => $s->slug,
                'department_name' => $s->department_name,
            ];
        }

        $this->output->set_content_type('application/json');
        echo json_encode($out);
    }
}
