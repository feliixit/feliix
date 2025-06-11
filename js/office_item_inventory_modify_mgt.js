var app = new Vue({
  el: "#app",
  data: {
    name: "",
    user_id : 0,
    month1: "",

    picked: "A",
    view_detail: false,

    submit: false,

    receive_records: [],
    record: {},

    total: 0,

    baseURL: "https://storage.googleapis.com/feliiximg/",

    proof_remark: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    pages_10: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],

    perPage: 20,

    is_approval: false,

    creators: {},
    checkers: {},
    approvers: {},

    fil_request_no_upper: "",
    fil_request_no_lower: "",
    
    fil_creator: "",
    fil_checker: "",
    fil_approver: "",

    fil_type: "",
    fil_status: [],
    fil_amount_type : "",
    
    fil_amount_upper: "",
    fil_amount_lower: "",

    fil_type_date: "",
    fil_date_start: "",
    fil_date_end: "",
    fil_update_start: "",
    fil_update_end: "",
 
    fil_keyword: "",

    od_factor1: "",
    od_factor1_order: "",
    od_factor2: "",
    od_factor2_order: "",
    
    projects: [],
    fil_project_name: "",
    od_project_name: "",

    check_name : "",
    pre_data : {
      check_name : '',
      kind : '',
      project_id : 0,
    },

    is_approver: false,
    is_checker : false,

  },

  created() {
  
    this.getUserName();

    let _this = this;
    let uri = window.location.href.split('?');

    let id = 0;

    if (uri.length >= 2)
    {
      let vars = uri[1].split('&');
      
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        {
          switch (tmp[0]) {
            case "id":
              id = tmp[1];
              break;
            case "fru":
              _this.fil_request_no_upper = tmp[1];
              break;
            case "frl":
              _this.fil_request_no_lower = tmp[1];
              break;
            case "fc":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "fch":
              _this.fil_checker = decodeURI(tmp[1]);
              break;
            case "fap":
              _this.fil_approver = decodeURI(tmp[1]);
              break;
            case "fds":
              _this.fil_date_start = tmp[1];
              break;
            case "fde":
              _this.fil_date_end = tmp[1];
              break;
            case "fus":
              _this.fil_update_start = tmp[1];
              break;
            case "fue":
              _this.fil_update_end = tmp[1];
              break;
            case "fk":
                _this.fil_keyword = decodeURI(tmp[1]);
                break;
            case "fds":
              _this.fil_date_start = tmp[1];
              break;
            case "fde":
              _this.fil_date_end = tmp[1];
              break;
            case "fus":
              _this.fil_update_start = tmp[1];
              break;
            case "fue":
              _this.fil_update_end = tmp[1];
              break;
            case "fs":
              var temp = tmp[1].split(",");
              if(temp.length > 0)
              {
                // remove the first empty element
                if(temp[0] == "")
                  temp.shift();
              }
                _this.fil_status = temp;
              break;
            case "of1":
              _this.od_factor1 = tmp[1];
              break;
            case "ofd1":
              _this.od_factor1_order = tmp[1];
              break;
            case "of2":
              _this.od_factor2 = tmp[1];
              break;
            case "ofd2":
              _this.od_factor2_order = tmp[1];
              break;
            case "pg":
              _this.pg = tmp[1];
              break;
            case "page":
              _this.page = tmp[1];
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }

    this.getLeaveCredit(id);
    // this.getProjectNames();
    this.getCreators();
    this.getCheckers();
    this.getApprovers();
    this.getAccess();
  },

  computed: {
    displayedRecord() {
      if(this.pg == 0)
        this.filter_apply_new();

      this.setPages();
      return this.paginate(this.receive_records);
    },

    
  },

  mounted() {
    
  },

  watch: {

    receive_records () {

      this.setPages();
    },

    // proof_id() {
    //   this.detail();
    // },
  },

  methods: {

    
    getAccess: function() {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);

      axios({
          method: 'get',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/office_item_inventory_access_control',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.is_checker = response.data.inventory_checker;
          _this.is_approver = response.data.inventory_approver;

      })
      .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
          })
      });
    },

    editRow:function(item){
      if(this.is_modifying)
          return;

      // keep pre data
      this.pre_data.check_name = item.check_name;

      this.is_modifying = true;

      item['is_edited'] = 0;

      this.check_name = item['check_name'];

      this.kind = item['kind'];

      console.log(item);
  },

  duplicateRow: function(item){
    let _id = item['id'];
    let _this = this;

    Swal.fire({
        title: "Duplicate",
        text: "Are you sure to duplicate this inventory modification record?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.value) {

          let me = _this;

            token = localStorage.getItem('token');
            var form_Data = new FormData();
            form_Data.append('jwt', token);

            form_Data.append('id', _id);

            //DELETE table_name WHERE ID=id;
            $.ajax({
                url: "api/office_item_inventory_modify_mgt_duplicate",
                type: "POST",
                contentType: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: form_Data,

                success: function (result) {
                    console.log(result);
                    Swal.fire({
                      html: result.message,
                      icon: "info",
                      confirmButtonText: "OK",
                    });
                    // me.clear();
                    _this.getLeaveCredit();
                },

                // show error message to user
                error: function (xhr, resp, text) {
                    // confirm("Error: " + xhr.responseText);
                }
            });

        } else {

        }
    });
  },

  check_valid: function(status, checker, approver){
    var ret_msg = "";

    if(status == 1)
    {
      if(!checker)
        ret_msg = "For inventory modification record with PHASE 1: Checker Creates Item List and Encodes Modified Qty, only checker is allowed to delete it.";
    }
    else if(status == 2)
    {
      if(!approver)
        ret_msg = "For inventory modification record with PHASE 2: Approver Reviews, only approver is allowed to delete it.";
    }
    else if(status == 3)
    {
      ret_msg = "For inventory modification record with PHASE 3: Inventory Modification Completed, no one is allowed to delete it.";
    }

    return ret_msg;
  },

  deleteRow: function(item){
    let _id = item['id'];
    let _this = this;

    let ret_msg = this.check_valid(item['status'], this.is_checker, this.is_approver);

    if(ret_msg != "")
    {
      Swal.fire({
        html: ret_msg,
        icon: "info",
        confirmButtonText: "OK",
      });

      return;
    }


    Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.value) {

          let me = _this;

            token = localStorage.getItem('token');
            var form_Data = new FormData();
            form_Data.append('jwt', token);

            form_Data.append('id', _id);

            //DELETE table_name WHERE ID=id;
            $.ajax({
                url: "api/office_item_inventory_modify_mgt_delete",
                type: "POST",
                contentType: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: form_Data,

                success: function (result) {
                    console.log(result);
                    Swal.fire({
                      html: result.message,
                      icon: "info",
                      confirmButtonText: "OK",
                    });
                    // me.clear();

                    _this.getLeaveCredit();
                },

                // show error message to user
                error: function (xhr, resp, text) {
                  // confirm("Error: " + xhr.responseText);
                }
            });

        } else {

        }
    });
  },

  confirmRow: function(item){
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

      form_Data.append('jwt', token);

      const check_name = this.check_name.trim();
      //var project_id = item['project_id'];
      // var project_id = this.project_id;

      // const project_name = this.shallowCopy(
      //             this.projects.find(
      //               (element) => element.id == project_id));

      // if(this.type == 'task') {
      //   form_Data.append('kind', this.kind);
      //   project_id = this.task_id;
      // }
                  

      form_Data.append('check_name', check_name);
      // form_Data.append('project_id', project_id);
      form_Data.append('id', item['id']);

      axios({
              method: 'post',
              headers: {
                  'Content-Type': 'multipart/form-data',
                  Authorization: `Bearer ${token}`
              },
              url: 'api/office_item_inventory_modify_mgt_edit_row',
              data: form_Data
          })
          .then(function(response) {
              //handle success
              console.log(response)

              //_this.clear();
              _this.check_name = '';
              // _this.project_id = 0;
              // _this.task_id = 0;
              // _this.kind = '';
              // _this.type = 'project';
              _this.getLeaveCredit();

          })
          .catch(function(response) {
              //handle error
              console.log(response)
          });

      item['is_edited'] = 1; 
      
      this.is_modifying = false;
  },

  cancelRow: function(item){
      this.check_name = '';
   
      item['project_id'] = this.pre_data.project_id;
      item['check_name'] = this.pre_data.check_name;
      item['kind'] = this.pre_data.kind;

      item['is_edited'] = 1; 

      this.type='project';

      this.is_modifying = false;
  },
  
    pre_page: function(){
      let tenPages = Math.floor((this.page - 1) / 10) + 1;

        this.page = parseInt(this.page) - 10;
        if(this.page < 1)
          this.page = 1;
 
        this.pages_10 = [];

        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;

        this.pages_10 = this.pages.slice(from, to);
      
    },

    nex_page: function(){
      let tenPages = Math.floor((this.page - 1) / 10) + 1;

      this.page = parseInt(this.page) + 10;
      if(this.page > this.pages.length)
        this.page = this.pages.length;

      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      let pages_10 = this.pages.slice(from, to);

      if(pages_10.length > 0)
        this.pages_10 = pages_10;

    },

    setPages() {
      console.log("setPages");
      this.pages = [];
      let numberOfPages = Math.ceil(this.total / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      if (this.page < 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function(posts) {
      console.log("paginate");
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let tenPages = Math.floor((this.page - 1) / 10);
      if(tenPages < 0)
        tenPages = 0;
      this.pages_10 = [];
      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      
      this.pages_10 = this.pages.slice(from, to);

/*
      let page = this.page;
      let perPage = this.perPage;
      from = page * perPage - perPage;
      to = page * perPage;
      
      return this.receive_records.slice(from, to);
*/
      return this.receive_records;
    },

    isNumeric: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    getLeaveCredit: function(id) {
      let _this = this;

      const params = {
        id: id,
        fru: _this.fil_request_no_upper,
        frl: _this.fil_request_no_lower,
        fc: _this.fil_creator,
        fch: _this.fil_checker,
        fap: _this.fil_approver,
        fk: _this.fil_keyword,
        fds : _this.fil_date_start,
        fde : _this.fil_date_end,
        fus : _this.fil_update_start,
        fue : _this.fil_update_end,
        fs: _this.fil_status.join(','),
        of1: _this.od_factor1,
        ofd1: _this.od_factor1_order,
        of2: _this.od_factor2,
        ofd2: _this.od_factor2_order,
        page: _this.page,
        size: _this.perPage,
      };

      let token = localStorage.getItem("accessToken");

      this.total = 0;

      axios
        .get("api/office_item_inventory_modify_mgt", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;

          if(_this.receive_records.length > 0)
          _this.total = _this.receive_records[0].cnt;
          else
            _this.total = 0;

          if(_this.pg !== 0)
            { 
              if (typeof _this.pg === 'undefined')
                _this.pg = 1;
                
              _this.page = _this.pg;
              _this.setPages();
            }
            
          if(_this.receive_records.length > 0 && id !== 0)
          {
            _this.proof_id = id;
              //_this.proof_id = _this.receive_records[0].id;
              //_this.detail();
          }
          else
          {_this.proof_id = 0;}
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    filter_apply_new: function(pg) {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;
      _this.page = 1;

      if(pg != undefined) {
        _this.page = pg;
      }

      window.location.href =
        "office_item_inventory_modify_mgt?" +
        "fru=" +
        _this.fil_request_no_upper +
        "&frl=" +
        _this.fil_request_no_lower +
        "&fc=" +
        _this.fil_creator +
        "&fch=" +
        _this.fil_checker +
        "&fap=" +
        _this.fil_approver +
        "&fds=" +
        _this.fil_date_start +
        "&fde=" +
        _this.fil_date_end +
        "&fus=" +
        _this.fil_update_start +
        "&fue=" +
        _this.fil_update_end +
        "&fk=" +
      _this.fil_keyword +
        "&fs=" +
        _this.fil_status.join(',') +
        "&of1=" +
        _this.od_factor1 +
        "&ofd1=" +
        _this.od_factor1_order +
        "&of2=" +
        _this.od_factor2 +
        "&ofd2=" +
        _this.od_factor2_order +
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },


    filter_apply: function(pg) {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      if(pg != undefined) {
        _this.page = pg;
      }

      window.location.href =
        "office_item_inventory_modify_mgt?" +
        "fru=" +
        _this.fil_request_no_upper +
        "&frl=" +
        _this.fil_request_no_lower +
        "&fc=" +
        _this.fil_creator +
        "&fch=" +
        _this.fil_checker +
        "&fap=" +
        _this.fil_approver +
        "&fds=" +
        _this.fil_date_start +
        "&fde=" +
        _this.fil_date_end +
        "&fus=" +
        _this.fil_update_start +
        "&fue=" +
        _this.fil_update_end +
        "&fk=" +
      _this.fil_keyword +
        "&fs=" +
        _this.fil_status.join(',') +
        "&of1=" +
        _this.od_factor1 +
        "&ofd1=" +
        _this.od_factor1_order +
        "&of2=" +
        _this.od_factor2 +
        "&ofd2=" +
        _this.od_factor2_order +
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },

    show_detail:function(id){
      this.proof_id = id;
    },

    filter_remove: function() {
      this.fil_request_no_upper = '';
      this.fil_request_no_lower = '';
      this.fil_creator = '';
      this.fil_checker = '';
      this.fil_approver = '';
      this.fil_type = "";
      this.fil_status = [];
      this.fil_amount_type = "";
    
      this.fil_amount_upper = "";
      this.fil_amount_lower = "";

      this.fil_type_date = "";
      this.fil_date_start = "";
      this.fil_date_end = "";

      this.fil_update_start = "";
      this.fil_update_end = "";

      this.fil_project_name = "";
 
      this.fil_keyword = "";
      
      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply_new(1);
    },

    getProjectNames: function() {
      let _this = this;
      let token = localStorage.getItem("accessToken");

      const params = {
        pid: 0,
      };

      axios
      .get("api/expense_get_exist_project_names", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
        .then(function(response) {
          _this.projects = response.data;
        })
        .catch(function(error) {
          //handle error
        });
    },

    order_remove: function() {
      this.od_factor1 = '';
      this.od_factor1_order = '';
      this.od_factor2 = '';
      this.od_factor2_order = '';
   
      
      document.getElementById("btn_sort").classList.remove("focus");
      document.getElementById("sort_dialog").classList.remove("show");

      this.receive_records = [];

      this.filter_apply(1);
    },

    order_clear() {
      document.getElementById("btn_sort").classList.remove("focus");
      document.getElementById("sort_dialog").classList.remove("show");
    },

    clear() {

      this.check_name = "";

      document.getElementById("btn_insert").classList.remove("focus");
      document.getElementById("insert_dialog").classList.remove("show");
    },

    filter_clear() {
      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");
    },

    getUserName: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/on_duty_get_myname",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.name = response.data.username;
          _this.user_id = response.data.user_id;
          //_this.is_manager = response.data.is_manager;
          //if (_this.name === "Glendon Wendell Co") _this.is_approval = true;
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    unCheckCheckbox() {
      for (i = 0; i < this.receive_records.length; i++) {
        this.receive_records[i].is_checked = false;
      }
      //$(".alone").prop("checked", false);
      //this.clicked = false;
    },

    showPic(pic) {
      Swal.fire({
        title: "Certificate of Diagnosis",
        text: "Click to close",
        imageUrl: "img/" + pic,
      });
    },

    approveReceiveRecord: function(id) {
      let _this = this;
      //targetId = this.record.id;
      var form_Data = new FormData();

      form_Data.append("crud", "app");
      form_Data.append("id", id);
      form_Data.append("remark", this.proof_remark);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/downpayment_proof_approval",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    rejectReceiveRecord: function(id) {
      let _this = this;
      //targetId = this.record.id;
      var form_Data = new FormData();

      form_Data.append("crud", "rej");
      form_Data.append("id", id);
      form_Data.append("remark", this.proof_remark);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/downpayment_proof_reject",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    detail: function() {
      let _this = this;

      //let favorite = [];

      //for (i = 0; i < this.receive_records.length; i++)
      //{
      //  if(this.receive_records[i].is_checked == 1)
      //    favorite.push(this.receive_records[i].sid);
      //}

      if (this.proof_id == 0) {
        //Swal.fire({
        //  text: "Please select row to see the detail!",
        //  icon: "warning",
        //  confirmButtonText: "OK",
        //});

        //$(window).scrollTop(0);
        this.view_detail = false;
        this.$refs.mask.style.display = 'none';
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      
      this.$refs.mask.style.display = 'block';
      this.view_detail = true;
    },

    approve: function() {
      let _this = this;

      if(this.submit)
        return;


      if (this.check_name.trim() == "") {
        Swal.fire({
          text: "Name of Inventory Modification is required!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }
      
      this.submit = true;

      var form_Data = new FormData();

      form_Data.append("check_name", this.check_name);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_item_inventory_modify_insert",
        data: form_Data,
      })
      .then(function(response) {
        //handle success
        //this.$forceUpdate();
        Swal.fire({
          text: response.data.message,
          icon: "info",
          confirmButtonText: "OK",
        });
        _this.resetForm();
      })
      .catch(function(response) {
        //handle error
        Swal.fire({
          text: response.data,
          icon: "warning",
          confirmButtonText: "OK",
        });
      }).finally(() => {
          _this.submit = false;
          }
        );

    },

    reject: function() {
      let _this = this;

      let favorite = [];

      var approve_record = false;

      for (i = 0; i < this.receive_records.length; i++) {
        if (this.receive_records[i].id == this.proof_id) {
          if (this.receive_records[i].status === "-1") {
            Swal.fire({
              text:
                "Rejected data cannot be rejected again! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if (this.receive_records[i].status === "1") {
            Swal.fire({
              text:
                "Approved data cannot be rejected! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          favorite.push(this.receive_records[i].id);
        }
      }

      if (favorite.length < 1) {
        Swal.fire({
          text: "Please select rows to reject!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      Swal.fire({
        title: "Are you sure to reject?",
        text: "Are you sure to reject apply?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.submit = true;
          _this.rejectReceiveRecord(favorite.join(", "));

          _this.resetForm();
          _this.unCheckCheckbox();
        }
      });
    },

    getCreators() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/office_item_inventory_modify_creators", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.creators = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getCheckers() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/office_item_inventory_modify_checkers", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.checkers = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getApprovers() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/office_item_inventory_modify_approvers", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.approvers = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    resetForm: function() {
      this.record = [];
      this.proof_id = 0;
      
      this.clear();

      this.getLeaveCredit();
      
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    
    export_office_item: function() {
      let _this = this;

      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append('id', this.record.id)

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_item_releasing_export",
        data: form_Data,
        responseType: "blob",
      })

        .then(function(response) {
          console.log(response);
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "Office Item Application Voucher_" + _this.record['request_no'] + ".docx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

  
    export_petty_list() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("fru", this.fil_request_no_upper);
      form_Data.append("frl", this.fil_request_no_lower);
      form_Data.append("fc", this.fil_creator);
      form_Data.append("fch", this.fil_checker);
      form_Data.append("fap", this.fil_approver);
      form_Data.append("fds", this.fil_date_start);
      form_Data.append("fde", this.fil_date_end);
      form_Data.append("fus", this.fil_update_start);
      form_Data.append("fue", this.fil_update_end);
      form_Data.append("fs", this.fil_status.join(','));
      form_Data.append("of1", this.od_factor1);
      form_Data.append("ofd1", this.od_factor1_order);
      form_Data.append("of2", this.od_factor2);
      form_Data.append("ofd2", this.od_factor2_order);
      form_Data.append("page", this.page);
      form_Data.append("size", this.perPage);

      axios({
        method: "post",
        url: "api/office_item_application_report_print",
        data: form_Data,
        responseType: "blob",
      })
          .then(function(response) {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
               
                  link.setAttribute('download', 'Office Item Application Report.xlsx');
               
                document.body.appendChild(link);
                link.click();

          })
          .catch(function(response) {
              //handle error
              console.log(response)
          });
    },



  },
});
