var app = new Vue({
  el: "#app",
  data: {
    name: "",
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

    perPage: 10,

    is_approval: false,

    creators: {},

    fil_start_date: "",
    fil_end_date: "",
    fil_creator: "",
    fil_category: "",
    fil_archive: "",

    date : "",
    
  },

  created() {

    this.fil_start_date = new Date().getFullYear() + "-" + "01-01";
    // add zeros to month and day
    if((new Date().getMonth()+1) < 10)
      this.fil_end_date = new Date().getFullYear() + "-" + "0" + (new Date().getMonth()+1) + "-" + "20";
    else
      this.fil_end_date = new Date().getFullYear() + "-" + (new Date().getMonth()+1) + "-" + "20";
  
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
            case "d":
              document.getElementById("start").value = tmp[1].substring(0,7);
              _this.fil_start_date = tmp[1];
              break;
            case "e":
              document.getElementById("end").value = tmp[1].substring(0,7);
              _this.fil_end_date = tmp[1];
              break;
            case "c":
              _this.fil_category = tmp[1];
              break;
            case "p":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "a":
              _this.fil_archive = tmp[1];
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

    this.getCreators();
  },

  computed: {

    
  },

  mounted() {
    document.getElementById("start").value = this.fil_start_date.substring(0,7);
    document.getElementById("end").value = this.fil_end_date.substring(0,7);
  },

  watch: {

    // receive_records () {

    //   this.setPages();
    // },

    proof_id() {
      this.detail();
    },
  },

  methods: {
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
        d: _this.fil_start_date,
        e: _this.fil_end_date,
        p: _this.fil_creator,
        c: _this.fil_category,
        a: _this.fil_archive,
      
        page: _this.page,
        size: _this.perPage,
      };

      let token = localStorage.getItem("accessToken");

      this.total = 0;

      axios
        .get("api/sales_dashboard_1", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
            _this.receive_records = response.data;
            _this.date = _this.receive_records[0].date;
    
        })
        .catch(function(error) {
          console.log(error);
        });
    },



    formatDate: function(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();
  
        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;
    
        return [year, month, day].join('-');
    },

    getPeriodDate: function() {
      let _this = this;
  
      if ($('#start').val()  === undefined)
          return;
  
        if ($('#start').val() === '')
          return;
  
 
  
      var sdate1 = '';
      var edate1 = '';
  
      var sdate2 = '';
      var edate2 = '';
  
      if($('#start').val())
      {
        var d1 = new Date($('#start').val() + '-01');
        sdate1 = d1.toISOString().slice(0,10);
        var newDate1 = new Date(d1.setMonth(d1.getMonth()+1));
        edate1 = newDate1.toISOString().slice(0,10);

        this.fil_start_date = sdate1;
        this.fil_end_date = edate1;
      }
  
      if($('#end').val())
      {
        var today = new Date($('#end').val());
        var d2 = new Date(today.getFullYear(), today.getMonth()+1, 0);
  
        sdate2 = today.toISOString().slice(0,10);
      
        edate2 = this.formatDate(d2);

        this.fil_end_date = edate2;
      }
  
    },


    filter_apply: function() {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      this.getPeriodDate();

      window.location.href =
        "sales_dashboard_1?" +
        "d=" +
        _this.fil_start_date +
        "&e=" +
        _this.fil_end_date +
        "&p=" +
        _this.fil_creator +
        "&c=" +
        _this.fil_category +
        "&a=" +
        _this.fil_archive +
      
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
      this.fil_creator = '';
      this.fil_category = '';
      this.fil_start_date = '';
      this.fil_end_date = '';
      this.fil_archive = '';
    
      document.getElementById("start").value = "";
      document.getElementById("end").value = "";

      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply();
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
          _this.is_manager = response.data.is_manager;
          if (_this.name === "Glendon Wendell Co") _this.is_approval = true;
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

    archive(pid)  {
      let _this = this;
      //targetId = this.record.id;
      var form_Data = new FormData();

      form_Data.append("id", pid);
      form_Data.append("ar", 1);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/sales_dashboard_archive",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.getLeaveCredit();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    unarchive(pid)  {
      let _this = this;
      //targetId = this.record.id;
      var form_Data = new FormData();

      form_Data.append("id", pid);
      form_Data.append("ar", 0);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/sales_dashboard_archive",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.getLeaveCredit();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
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

    getCreators() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      let params = {
        s: "",
      };

      axios
        .get("api/admin/project_creators", {
          params,
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

    resetForm: function() {
      this.record = [];
      this.proof_id = 0;
      this.getLeaveCredit(0);
      
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    export_petty() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("d", this.fil_start_date);
      form_Data.append("e", this.fil_end_date);
      form_Data.append("p", this.fil_creator);
      form_Data.append("c", this.fil_category);
      form_Data.append("a", this.fil_archive);
    

      axios({
        method: "post",
        url: "api/sales_dashboard_1_print",
        data: form_Data,
        responseType: "blob",
      })
      .then(function(response) {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            
              link.setAttribute('download', 'Sales Dashboard Report.xlsx');
            
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
