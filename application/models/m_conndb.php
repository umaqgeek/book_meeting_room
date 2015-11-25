<?php
  class M_conndb extends CI_Model {
	  
	  function getAll($table) {
		  $this->db->select('*');
		  $this->db->from($table);
		  $q = $this->db->get();
		  if($q->num_rows() > 0) {
			  foreach($q->result() as $r) {
				  $d[] = $r;
			  }
			  return $d;
		  }
	  }
	  
	  function get($table, $pri, $id) {
		  $this->db->select('*');
		  $this->db->from($table);
		  $this->db->where($pri, $id);
		  $q = $this->db->get();
		  if($q->num_rows() > 0) {
			  foreach($q->result() as $r) {
				  $d[] = $r;
			  }
			  return $d;
		  }
	  }
	  
	  function add($table, $data) {
		  if($this->db->insert($table, $data)) {
			  return $this->db->insert_id();
		  } else {
			  return 0;
		  }
	  }
	  
	  function edit($table, $pri, $id, $data) {
		  $this->db->where($pri, $id);
		  return $this->db->update($table, $data);
	  }
	  
	  function delete($table, $pri, $id) {
		  $this->db->where($pri, $id);
		  return $this->db->delete($table);
	  }
          
          function dateBeforeDelete() {
              /*
               * DELETE FROM tempahan WHERE date(t_date) < date(now())
               */
              $this->db->where('date(t_date) < date(now())');
              return $this->db->delete('tempahan');
          }
	
  }

?>