var app = new Vue({
  el: "#app",
  data: {
    project_category: "",
    client_type: "",
    priority: "",
    status: "",
    reason: "",
    project_name: "",
    special_note: "",

    probability: 0,

    receive_records: [],
    record: [],

    total: 0,

    record_filter: [],

    categorys: {},
    client_types: {},
    priorities: {},
    statuses: {},
    stages: {},
    creators: {},

    department:'',
    title: '',
    username:'',

    submit: false,
    // paging
    page: 1,
    pg:0,
    //perPage: 10,
    pages: [],

    pages_10: [],

    // paging
    quote_page: 1,
    //perPage: 10,
    quote_pages: [],

    quote_keyword: "",

    po_page: 1,
    po_pages: [],
    po_keyword: "",

    other_page: 1,
    other_pages: [],
    other_keyword: "",

    // paging
    pay_page: 1,
    //perPage: 10,
    pay_pages: [],

    payment_keyword: "",

    baseURL: "https://storage.cloud.google.com/",

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],
    perPage: 10,

    prof_canSub: true,
    prof_remark: "",
    payment_type: "",

    payment_method_1: "",
    payment_method_other: "",

    prof_fileArray: [],

    quote_canSub: true,
    quote_remark: "",
    quote_fileArray: [],

    other_type: "2",
    other_remark: "",
    date_data_submission: "",
    other_canSub: true,
    other_fileArray: [],

    po_type: "",
    po_remark: "",
    po_canSub: true,
    po_fileArray: [],

    itemPage: 5,

    fil_category: "",
    fil_status: "",
    fil_creator: "",

    fil_amount_upper: "",
    fil_amount_lower: "",
    fil_payment_upper: "",
    fil_payment_lower: "",
    fil_ar_upper: "",
    fil_ar_lower: "",

    fil_amount_upper_eq: "",
    fil_amount_lower_eq: "",
    fil_payment_upper_eq: "",
    fil_payment_lower_eq: "",
    fil_ar_upper_eq: "",
    fil_ar_lower_eq: "",

    fil_aging: "",

    fil_keyowrd: "",
    fil_keyowrd_p: "",

    fil_proof : "",

    od_factor1: "",
    od_factor1_order: "",
    od_factor2: "",
    od_factor2_order: "",

    id: "",

    proof_id: 0,
    view_detail: false,

    view_a: false,
    view_b: false,
    view_c: false,
    view_d: false,
    view_e: false,
    view_f: false,

    view_proof : false,
  },

  created() {
    let _this = this;
    let uri = window.location.href.split("?");
    if (uri.length >= 2) {
      let vars = uri[1].split("&");

      let tmp = "";
      vars.forEach(async function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
          switch (tmp[0]) {
            case "fc":
              _this.fil_category = tmp[1];
              break;
            case "fs":
              _this.fil_status = tmp[1];
              break;
            case "fp":
              _this.fil_proof = tmp[1];
              break;
            case "ft":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "fal":
              _this.fil_amount_lower = tmp[1];
              break;
            case "fau":
              _this.fil_amount_upper = tmp[1];
              break;
            case "fpl":
              _this.fil_payment_lower = tmp[1];
              break;
            case "fpu":
              _this.fil_payment_upper = tmp[1];
              break;
            case "frl":
              _this.fil_ar_lower = tmp[1];
              break;
            case "fru":
              _this.fil_ar_upper = tmp[1];
              break;
            case "fal_eq":
              _this.fil_amount_lower_eq = tmp[1];
              break;
            case "fau_eq":
              _this.fil_amount_upper_eq = tmp[1];
              break;
            case "fpl_eq":
              _this.fil_payment_lower_eq = tmp[1];
              break;
            case "fpu_eq":
              _this.fil_payment_upper_eq = tmp[1];
              break;
            case "frl_eq":
              _this.fil_ar_lower_eq = tmp[1];
              break;
            case "fru_eq":
              _this.fil_ar_upper_eq = tmp[1];
              break;
            case "aging":
              _this.fil_aging = tmp[1];
              break;
            case "fk":
              _this.fil_keyowrd = decodeURI(tmp[1]);
              break;
            case "fkp":
              _this.fil_keyowrd_p = decodeURI(tmp[1]);
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
            case "id":
              _this.id = tmp[1];
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
        }
      });
    }

    this.getRecords();

    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
    this.getStatuses();
    this.getStages();
    this.getCreators();
    this.getUserName();
  },

  computed: {
    displayedPosts() {
      if(this.pg == 0)
        this.filter_apply_new();

      this.setPages();
      return this.paginate(this.receive_records);
    },

    displayedQuote() {
      this.setQuotePages();
      return this.quote_paginate(this.record.quote);
    },

    displayedPayment() {
      this.setPayPages();
      return this.pay_paginate(this.record.payment);
    },

    displayedPo() {
      this.setPoPages();
      return this.po_paginate(this.record.client_po);
    },

    displayedOther() {
      this.setOtherPages();
      return this.other_paginate(this.record.client_other);
    },

  },

  mounted() {
 
  },

  watch: {
 
    proof_id() {
      this.detail();
    },

  },

  methods: {
    show_proof : function() {
    
      if(this.record.special == 's')
        this.view_proof = false;
      else
        this.view_proof = true;

      if(this.username.toLowerCase() ==='dennis lin' || this.username.toLowerCase() ==='kristel tan' || this.username.toLowerCase() ==='glendon wendell co' || this.username.toLowerCase() ==='kuan')
        this.view_proof = true;


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

    setQuotePages() {
      console.log("setQuotePages");
      this.quote_pages = [];
      let numberOfPages = Math.ceil(this.record.quote.length / this.perPage);

      if (numberOfPages == 1) this.quote_page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.quote_pages.push(index);
      }
    },

    setPayPages() {
      console.log("setPayPages");
      this.pay_pages = [];
      let numberOfPages = Math.ceil(this.record.payment.length / this.perPage);

      if (numberOfPages == 1) this.pay_page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pay_pages.push(index);
      }
    },

    setPoPages() {
      console.log("setPoPages");
      this.po_pages = [];
      let numberOfPages = Math.ceil(this.record.client_po.length / this.itemPage);

      if (numberOfPages == 1) this.po_page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.po_pages.push(index);
      }
    },

    setOtherPages() {
      console.log("setOtherPages");
      this.other_pages = [];
      let numberOfPages = Math.ceil(this.record.client_other.length / this.itemPage);

      if (numberOfPages == 1) this.other_page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.other_pages.push(index);
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

      return this.receive_records;
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

    quote_paginate: function(posts) {
      console.log("quote_paginate");
      if (this.quote_page < 1) this.quote_page = 1;
      if (this.quote_page > this.quote_pages.length)
        this.quote_page = this.quote_pages.length;

      let page = this.quote_page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.record.quote.slice(from, to);
    },

    po_paginate: function(posts) {
      console.log("po_paginate");
      if (this.po_page < 1) this.po_page = 1;
      if (this.po_page > this.po_pages.length)
        this.po_page = this.po_pages.length;

      let page = this.po_page;
      let perPage = this.itemPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.record.client_po.slice(from, to);
    },

    other_paginate: function(posts) {
      console.log("other_paginate");
      if (this.other_page < 1) this.other_page = 1;
      if (this.other_page > this.other_pages.length)
        this.other_page = this.other_pages.length;

      let page = this.other_page;
      let perPage = this.itemPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.record.client_other.slice(from, to);
    },

    pay_paginate: function(posts) {
      console.log("pay_paginate");
      if (this.pay_page < 1) this.pay_page = 1;
      if (this.pay_page > this.pay_pages.length)
        this.pay_page = this.pay_pages.length;

      let page = this.pay_page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;

      return this.record.payment.slice(from, to);
    },

    show_detail: function(id) {
      this.proof_id = id;
    },

    hide_detail: function() {
      this.proof_id = 0;

      this.view_a = false;
      this.view_b = false;
      this.view_c = false;
      this.view_d = false;
      this.view_e = false;
      this.view_f = false;
    },

    togle_a: function() {
      this.view_a = true;
      this.view_b = false;
      this.view_c = false;
      this.view_d = false;
      this.view_e = false;
      this.view_f = false;
    },

    togle_b: function() {
      this.view_a = false;
      this.view_b = true;
      this.view_c = false;
      this.view_d = false;
      this.view_e = false;
      this.view_f = false;
    },

    togle_c: function() {
      this.view_a = false;
      this.view_b = false;
      this.view_c = true;
      this.view_d = false;
      this.view_e = false;
      this.view_f = false;
    },

    togle_d: function() {
      this.view_a = false;
      this.view_b = false;
      this.view_c = false;
      this.view_d = true;
      this.view_e = false;
      this.view_f = false;
    },

    togle_e: function() {
      this.view_a = false;
      this.view_b = false;
      this.view_c = false;
      this.view_d = false;
      this.view_e = true;
      this.view_f = false;
    },

    togle_f: function() {
      this.view_a = false;
      this.view_b = false;
      this.view_c = false;
      this.view_d = false;
      this.view_e = false;
      this.view_f = true;
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        this.view_detail = false;
        this.$refs.mask.style.display = "none";
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.show_proof();

      this.$refs.mask.style.display = "block";
      this.view_detail = true;
      this.view_a = true;
    },

    filter_apply_new: function() {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;
      _this.page = 1;

      window.location.href =
        "quotation_and_payment_mgt_v2?" +
        "fc=" +
        _this.fil_category +
        "&fs=" +
        _this.fil_status +
        "&fp=" +
        _this.fil_proof +
        "&ft=" +
        _this.fil_creator +
        "&fal=" +
        _this.fil_amount_lower +
        "&fau=" +
        _this.fil_amount_upper +
        "&fpl=" +
        _this.fil_payment_lower +
        "&fpu=" +
        _this.fil_payment_upper +
        "&frl=" +
        _this.fil_ar_lower +
        "&fru=" +
        _this.fil_ar_upper +
        "&fal_eq=" +
        _this.fil_amount_lower_eq +
        "&fau_eq=" +
        _this.fil_amount_upper_eq +
        "&fpl_eq=" +
        _this.fil_payment_lower_eq +
        "&fpu_eq=" +
        _this.fil_payment_upper_eq +
        "&frl_eq=" +
        _this.fil_ar_lower_eq +
        "&fru_eq=" +
        _this.fil_ar_upper_eq +
        "&aging=" +
        _this.fil_aging +
        "&fk=" +
        _this.fil_keyowrd +
        "&fkp=" +
        _this.fil_keyowrd_p +
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

    filter_apply: function() {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      window.location.href =
        "quotation_and_payment_mgt_v2?" +
        "fc=" +
        _this.fil_category +
        "&fs=" +
        _this.fil_status +
        "&fp=" +
        _this.fil_proof +
        "&ft=" +
        _this.fil_creator +
        "&fal=" +
        _this.fil_amount_lower +
        "&fau=" +
        _this.fil_amount_upper +
        "&fpl=" +
        _this.fil_payment_lower +
        "&fpu=" +
        _this.fil_payment_upper +
        "&frl=" +
        _this.fil_ar_lower +
        "&fru=" +
        _this.fil_ar_upper +
        "&fal_eq=" +
        _this.fil_amount_lower_eq +
        "&fau_eq=" +
        _this.fil_amount_upper_eq +
        "&fpl_eq=" +
        _this.fil_payment_lower_eq +
        "&fpu_eq=" +
        _this.fil_payment_upper_eq +
        "&frl_eq=" +
        _this.fil_ar_lower_eq +
        "&fru_eq=" +
        _this.fil_ar_upper_eq +
        "&aging=" +
        _this.fil_aging +
        "&fk=" +
        _this.fil_keyowrd +
        "&fkp=" +
        _this.fil_keyowrd_p +
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

    getRecords: function(keyword) {
      let _this = this;

      const params = {
        id: _this.id,
        fc: _this.fil_category,
        fs: _this.fil_status,
        fp: _this.fil_proof,
        ft: _this.fil_creator,
        fal: _this.fil_amount_lower,
        fau: _this.fil_amount_upper,
        fpl: _this.fil_payment_lower,
        fpu: _this.fil_payment_upper,
        frl: _this.fil_ar_lower,
        fru: _this.fil_ar_upper,
        fal_eq: _this.fil_amount_lower_eq,
        fau_eq: _this.fil_amount_upper_eq,
        fpl_eq: _this.fil_payment_lower_eq,
        fpu_eq: _this.fil_payment_upper_eq,
        frl_eq: _this.fil_ar_lower_eq,
        fru_eq: _this.fil_ar_upper_eq,
        aging: _this.fil_aging,
        fk: _this.fil_keyowrd,
        fkp: _this.fil_keyowrd_p,
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
        .get("api/quotation_payment_mgt", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.receive_records = res.data;
            _this.total = _this.receive_records[0].cnt;
            _this.quote_search();
            _this.payment_search();

            if(_this.pg !== 0)
            { 
              _this.page = _this.pg;
              _this.setPages();
            }

            if(_this.proof_id !== 0)
            {
              _this.record = _this.shallowCopy(
                _this.receive_records.find((element) => element.id == _this.proof_id)
              );
            }

          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getProjectCategorys() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_category", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.categorys = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getClientTypes() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_client_type", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.client_types = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getPrioritys() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_priority", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.priorities = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getStatuses() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_status", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.statuses = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getStages() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_stage", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.stages = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getCreators() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_creators", {
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

    getLeaveCredit: function() {
      let _this = this;

      axios
        .get("api/ammend")
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
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
          _this.username = response.data.username;
          _this.is_manager = response.data.is_manager;
          _this.department = response.data.department;
          _this.title = response.data.title;
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

    quote_search: function() {
      if (this.proof_id == 0) return;
      this.record_filter = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      this.record.quote = this.record_filter.quote.filter((searchResult) =>
        searchResult.searchstr.match(this.quote_keyword.toLowerCase().trim()) || searchResult.create === this.quote_keyword.toLowerCase().trim()
      );
    },

    payment_search: function() {
      if (this.proof_id == 0) return;
      this.record_filter = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      this.record.payment = this.record_filter.payment.filter((searchResult) =>
        searchResult.searchstr.match(this.payment_keyword.toLowerCase().trim()) || searchResult.create === this.payment_keyword.toLowerCase().trim()
      );
    },

    po_search: function() {
      if (this.proof_id == 0) return;
      this.record_filter = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      this.record.client_po = this.record_filter.client_po.filter((searchResult) =>
        searchResult.searchstr.match(this.po_keyword.toLowerCase().trim()) || searchResult.create === this.po_keyword.toLowerCase().trim()
      );
    },

    other_search: function() {
      if (this.proof_id == 0) return;
      this.record_filter = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      this.record.client_other = this.record_filter.client_other.filter((searchResult) =>
        searchResult.searchstr.match(this.other_keyword.toLowerCase().trim()) || searchResult.create === this.other_keyword.toLowerCase().trim() 
      );
    },

    approve: function() {
      let _this = this;

      if (this.project_name.trim() == "") {
        Swal.fire({
          text: "Please enter Project Name!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.project_category.trim() == "") {
        Swal.fire({
          text: "Please select Project Category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.client_type.trim() == "") {
        Swal.fire({
          text: "Please select Client Type!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.priority.trim() == "") {
        Swal.fire({
          text: "Please select Priority!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append("project_name", this.project_name);
      form_Data.append("project_category", this.project_category);
      form_Data.append("client_type", this.client_type);
      form_Data.append("priority", this.priority);
      form_Data.append("status", this.status);
      form_Data.append("reason", this.reason);
      form_Data.append("probability", this.probability);
      form_Data.append("special_note", this.special_note);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project01_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.clear();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    clear: function() {
      this.project_name = "";
      this.project_category = "";
      this.client_type = "";
      this.priority = "";
      this.status = "";
      this.reason = "";
      this.probability = "";
      this.special_note = "";

      document.getElementById("insert_dialog").classList.remove("show");

      this.receive_records = [];

      this.getRecords();
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    filter_remove: function() {
      this.fil_category = '';
      this.fil_status = '';
      this.fil_proof = '';
      this.fil_creator = '';
      this.fil_amount_upper = '';
      this.fil_amount_lower = '';
      this.fil_payment_upper = '';
      this.fil_payment_lower = '';
      this.fil_ar_upper = '';
      this.fil_ar_lower = '';

      this.fil_amount_upper_eq = '';
      this.fil_amount_lower_eq = '';
      this.fil_payment_upper_eq = '';
      this.fil_payment_lower_eq = '';
      this.fil_ar_upper_eq = '';
      this.fil_ar_lower_eq = '';

      this.fil_aging = '';

      this.fil_keyowrd = '';
      this.fil_keyowrd_p = '';
      
      document.getElementById("dialog_f1").classList.remove("focus");
      document.getElementById("add_f1").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply_new();
    },

    order_remove: function() {
      this.od_factor1 = '';
      this.od_factor1_order = '';
      this.od_factor2 = '';
      this.od_factor2_order = '';
   
      
      document.getElementById("dialog_a1").classList.remove("focus");
      document.getElementById("add_a1").classList.remove("show");

      this.receive_records = [];

      this.getRecords();
    },

    order_clear() {
      document.getElementById("dialog_a1").classList.remove("focus");
      document.getElementById("add_a1").classList.remove("show");
    },

    filter_clear() {
      document.getElementById("dialog_f1").classList.remove("focus");
      document.getElementById("add_f1").classList.remove("show");
    },

    quote_deleteFile(index) {
      this.quote_fileArray.splice(index, 1);
      var fileTarget = this.$refs.quote_file;
      fileTarget.value = "";
    },

    final_quotation_clear() {
      var finals = document.getElementsByName("quotation_id");
      for (var i = 0; i < finals.length; i++) {
        finals[i].checked = false;
      }
    },

    client_po_clear() {
      var finals = document.getElementsByName("po_id");
      for (var i = 0; i < finals.length; i++) {
        finals[i].checked = false;
      }
    },

    client_other_clear() {
      var finals = document.getElementsByName("other_id");
      for (var i = 0; i < finals.length; i++) {
        finals[i].checked = false;
      }
    },

    payment_clear() {
      var finals = document.getElementsByName("payment_id");
      for (var i = 0; i < finals.length; i++) {
        finals[i].checked = false;
      }
    },

    payment_withdraw() {
      var candeletebool = this.CanSaveFinalAmount();

      var finals = document.getElementsByName("payment_id");
      var final_id = "";
      var final_cnt = 0;
      for (var i = 0; i < finals.length; i++) {
        if (finals[i].checked)
        {

          var record = this.shallowCopy(
            this.record.payment.find((element) => element.id == finals[i].value)
          );

          if (record.checked === "-1") {
            Swal.fire({
              text:
                "Checked proof cannot be deleted.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if (record.checked === "1") {
            Swal.fire({
              text:
                "Checked proof cannot be deleted.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if(!candeletebool)
          {
            if(record.username == this.username)
            { 
              final_id += finals[i].value + ",";
              final_cnt++;
            }
          }
          else
          {
            final_id += finals[i].value + ",";
            final_cnt++;
          }
        }
      }

      if (final_id !== "") 
        final_id = final_id.slice(0, -1);
      else
      { 
        if(!candeletebool)
        { 
          Swal.fire({
            text: "Permission denied",
            icon: "info",
            confirmButtonText: "OK",
          });
        }
        else
        {
          Swal.fire({
            text: "Please select records to withdraw",
            icon: "info",
            confirmButtonText: "OK",
          });
        }
        return;
      }

      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("final", final_id);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_payment_withdraw",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: "Finished deleting " + final_cnt + " selected record(s) " + response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.getRecords();
          _this.payment_clear();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    final_quotation() {

      if(!this.CanSaveFinalAmount()){
        Swal.fire({
          text: "Permission denied",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }

      var finals = document.getElementsByName("quotation_id");
      var final_id = "";
      for (var i = 0; i < finals.length; i++) {
        if (finals[i].checked) final_id += finals[i].value + ",";
      }
      if (final_id !== "") final_id = final_id.slice(0, -1);

      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("final", final_id);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_final_quotation",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.getRecords();
          _this.final_quotation_clear();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    CanSaveFinalAmount() {
      var can_save = false;

      if(this.department.trim().toUpperCase() == 'SALES')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT CUSTOMER VALUE DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'CUSTOMER VALUE DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'LIGHTING')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'LIGHTING VALUE CREATION DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'OFFICE')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'OFFICE SPACE VALUE CREATION DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'DESIGN')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT BRAND MANAGER')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'BRAND MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'ENGINEERING')
      { 
        if(this.title.trim().toUpperCase() == 'ENGINEERING MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'ADMIN')
      { 
        if(this.title.trim().toUpperCase() == 'OPERATIONS MANAGER')
          can_save = true;
        if(this.title.trim().toUpperCase() == 'SR. OFFICE ADMIN ASSOCIATE')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'TW')
      { 
        if(this.title.trim().toUpperCase() == 'SUPPLY CHAIN MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == '')
      { 
        if(this.title.trim().toUpperCase() == 'OWNER')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'MANAGING DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'CHIEF ADVISOR')
          can_save = true;
      }

      if(this.username.trim() == 'Marie Kayla Patricia Dequina' || this.username.trim() == 'Stephanie De dios')
      can_save = true;
      
      if(this.username.trim() == this.record.username.trim())
        can_save = true;
      
      return can_save;
    },


    CanDeleteClientPo() {
      var can_save = false;

      if(this.department.trim().toUpperCase() == 'SALES')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT CUSTOMER VALUE DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'CUSTOMER VALUE DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'LIGHTING')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT LIGHTING VALUE CREATION DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'LIGHTING VALUE CREATION DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'OFFICE')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'OFFICE SPACE VALUE CREATION DIRECTOR')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'DESIGN')
      { 
        if(this.title.trim().toUpperCase() == 'ASSISTANT BRAND MANAGER')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'BRAND MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'ENGINEERING')
      { 
        if(this.title.trim().toUpperCase() == 'ENGINEERING MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'ADMIN')
      { 
        if(this.title.trim().toUpperCase() == 'OPERATIONS MANAGER')
          can_save = true;
        if(this.title.trim().toUpperCase() == 'SR. OFFICE ADMIN ASSOCIATE')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == 'TW')
      { 
        if(this.title.trim().toUpperCase() == 'SUPPLY CHAIN MANAGER')
          can_save = true;
      }

      if(this.department.trim().toUpperCase() == '')
      { 
        if(this.title.trim().toUpperCase() == 'OWNER')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'MANAGING DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'CHIEF ADVISOR')
          can_save = true;
      }

      if(this.username.trim() == 'Marie Kayla Patricia Dequina' || this.username.trim() == 'Stephanie De dios')
      can_save = true;
    
      return can_save;
    },

    final_amount() {
      if(!this.CanSaveFinalAmount()){
        Swal.fire({
          text: "Permission denied",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }


      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      var final_amount = document.getElementById('final_amount').value;
      var tax_withheld = document.getElementById('tax_withheld').value;
      var billing_name = document.getElementById('billing_name').value;

      if(isNaN(final_amount)) {
        Swal.fire({
          text: "Please enter a valid amount",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("amount", final_amount);
      form_Data.append("tax_withheld", tax_withheld);
      form_Data.append("billing_name", billing_name);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_final_amount",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.getRecords();
   
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    delete_quotation() {
      let _this = this;

      var candeletemyfile = "";
      var candeletemyfilecnt = 0;
      var selectedcnt = 0;

      var candeletebool = this.CanSaveFinalAmount();
   
      var to_delete = document.getElementsByName("quotation_id");
  
      for (var i = 0; i < to_delete.length; i++) {
        if (to_delete[i].checked){

          var record = this.shallowCopy(
            this.record.quote.find((element) => element.id == to_delete[i].value)
          );
          if(record.username == this.username)
          {
            candeletemyfile += to_delete[i].value + ",";
            candeletemyfilecnt++;
          }
          selectedcnt++;
          
        }
      }

      if (candeletemyfile !== "") 
        candeletemyfile = candeletemyfile.slice(0, -1);
    
      if(selectedcnt < 1)
      { 
        Swal.fire({
          text: "Please select a record to delete",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }

      if(candeletemyfilecnt < 1 && !candeletebool)
      { 
        Swal.fire({
          text: "Permission denied",
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
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          
          var finals = document.getElementsByName("quotation_id");
          var final_id = "";
          if(candeletebool)
          {
            candeletemyfilecnt = 0;
            for (var i = 0; i < finals.length; i++) {
              if (finals[i].checked) {
                final_id += finals[i].value + ",";
                candeletemyfilecnt++;
              }
            }
            if (final_id !== "") final_id = final_id.slice(0, -1);
          }

          var form_Data = new FormData();
          var token = localStorage.getItem("token");

          form_Data.append("jwt", token);
          form_Data.append("pid", this.proof_id);
          if(!candeletebool)
            form_Data.append("final", candeletemyfile);
          else
            form_Data.append("final", final_id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
            url: "api/project_delete_quotation",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                text: "Finished deleting " + candeletemyfilecnt + " selected record(s) " + response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
              _this.getRecords();
              _this.final_quotation_clear();
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: response.data,
                icon: "info",
                confirmButtonText: "OK",
              });
            });
        } else {
          return;
        }
      });
    },


    delete_user_po() {
      let _this = this;

      var candeletemyfile = "";
      var candeletemyfilecnt = 0;
      var selectedcnt = 0;

      var candeletebool = this.CanDeleteClientPo();
   
      var to_delete = document.getElementsByName("po_id");
  
      for (var i = 0; i < to_delete.length; i++) {
        if (to_delete[i].checked){

          var record = this.shallowCopy(
            this.record.client_po.find((element) => element.id == to_delete[i].value)
          );
          if(record.username == this.username)
          {
            candeletemyfile += to_delete[i].value + ",";
            candeletemyfilecnt++;
          }
          selectedcnt++;
          
        }
      }

      if (candeletemyfile !== "") 
        candeletemyfile = candeletemyfile.slice(0, -1);
    
      if(selectedcnt < 1)
      { 
        Swal.fire({
          text: "Please select a record to delete",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }

      if(candeletemyfilecnt < 1 && !candeletebool)
      { 
        Swal.fire({
          text: "Permission denied",
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
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          
          var finals = document.getElementsByName("po_id");
          var final_id = "";
          if(candeletebool)
          {
            candeletemyfilecnt = 0;
            for (var i = 0; i < finals.length; i++) {
              if (finals[i].checked) {
                final_id += finals[i].value + ",";
                candeletemyfilecnt++;
              }
            }
            if (final_id !== "") final_id = final_id.slice(0, -1);
          }

          var form_Data = new FormData();
          var token = localStorage.getItem("token");

          form_Data.append("jwt", token);
          form_Data.append("pid", this.proof_id);
          if(!candeletebool)
            form_Data.append("final", candeletemyfile);
          else
            form_Data.append("final", final_id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
            url: "api/project_delete_client_po",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                text: "Finished deleting " + candeletemyfilecnt + " selected record(s) " + response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
              _this.getRecords();
              _this.client_po_clear();
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: response.data,
                icon: "info",
                confirmButtonText: "OK",
              });
            });
        } else {
          return;
        }
      });
    },

    delete_other() {
      let _this = this;

      var candeletemyfile = "";
      var candeletemyfilecnt = 0;
      var selectedcnt = 0;

      var candeletebool = this.CanDeleteClientPo();
   
      var to_delete = document.getElementsByName("other_id");
  
      for (var i = 0; i < to_delete.length; i++) {
        if (to_delete[i].checked){

          var record = this.shallowCopy(
            this.record.client_other.find((element) => element.id == to_delete[i].value)
          );
          if(record.username == this.username)
          {
            candeletemyfile += to_delete[i].value + ",";
            candeletemyfilecnt++;
          }
          selectedcnt++;
          
        }
      }

      if (candeletemyfile !== "") 
        candeletemyfile = candeletemyfile.slice(0, -1);
    
      if(selectedcnt < 1)
      { 
        Swal.fire({
          text: "Please select a record to delete",
          icon: "info",
          confirmButtonText: "OK",
        });

        return;
      }

      if(candeletemyfilecnt < 1 && !candeletebool)
      { 
        Swal.fire({
          text: "Permission denied",
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
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          
          var finals = document.getElementsByName("other_id");
          var final_id = "";
          if(candeletebool)
          {
            candeletemyfilecnt = 0;
            for (var i = 0; i < finals.length; i++) {
              if (finals[i].checked) {
                final_id += finals[i].value + ",";
                candeletemyfilecnt++;
              }
            }
            if (final_id !== "") final_id = final_id.slice(0, -1);
          }

          var form_Data = new FormData();
          var token = localStorage.getItem("token");

          form_Data.append("jwt", token);
          form_Data.append("pid", this.proof_id);
          if(!candeletebool)
            form_Data.append("final", candeletemyfile);
          else
            form_Data.append("final", final_id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
            url: "api/project_delete_client_po",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                text: "Finished deleting " + candeletemyfilecnt + " selected record(s) " + response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
              _this.getRecords();
              _this.client_other_clear();
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: response.data,
                icon: "info",
                confirmButtonText: "OK",
              });
            });
        } else {
          return;
        }
      });
    },

    quote_changeFile() {
      var fileTarget = this.$refs.quote_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.quote_fileArray.indexOf(fileTarget.files[i]) == -1 ||
          this.quote_fileArray.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.quote_fileArray.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    quote_clear() {
      this.quote_remark = "";
      this.quote_fileArray = [];
      this.$refs.quote_file.value = "";

      this.quote_canSub = true;

      this.view_detail = false;
      this.$refs.mask.style.display = "none";
      this.view_a = false;
    },

    quote_create() {
      let _this = this;
      if (this.quote_remark.trim() == "") {
        Swal.fire({
          text: "Please enter description!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("remark", this.quote_remark.trim());

      for (var i = 0; i < this.quote_fileArray.length; i++) {
        let file = this.quote_fileArray[i];
        form_Data.append("files[" + i + "]", file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_quote_new",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.quote_clear();
          _this.getRecords();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    prof_deleteFile(index) {
      this.prof_fileArray.splice(index, 1);
      var fileTarget = this.$refs.prof_file;
      fileTarget.value = "";
    },

    prof_changeFile() {
      var fileTarget = this.$refs.prof_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.prof_fileArray.indexOf(fileTarget.files[i]) == -1 ||
          this.prof_fileArray.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.prof_fileArray.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    prof_clear() {
      this.prof_remark = "";
      this.payment_type = "";
      this.payment_method_1 = "";
      this.payment_method_other = "";
      this.prof_fileArray = [];
      this.$refs.prof_file.value = "";

      this.prof_canSub = true;

      this.view_detail = false;
      this.$refs.mask.style.display = "none";
      this.view_c = false;

      this.view_proof = false;
    },

    prof_create() {
      let _this = this;
      if (this.prof_remark.trim() == "") {
        Swal.fire({
          text: "Please enter Remarks!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if(this.record.special == "sn" && this.record.final_amount == null)
      {
        Swal.fire({
          text: "Please encode the amount of this project before uploading the proof.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.payment_method_1 == 'Other' && this.payment_method_other.trim() == "")
      {
        Swal.fire({
          text: "Please Specify Method of Payment!!",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      _this.submit = true;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      if(this.payment_method_1 != 'Other')
        this.payment_method_other = "";

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("remark", this.prof_remark.trim());
      form_Data.append("kind", this.payment_type.trim());
      form_Data.append("payment_method_1", this.payment_method_1.trim());
      form_Data.append("payment_method_other", this.payment_method_other.trim());

      for (var i = 0; i < this.prof_fileArray.length; i++) {
        let file = this.prof_fileArray[i];
        form_Data.append("files[" + i + "]", file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_proof_new",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.prof_clear();
          _this.getRecords();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    other_deleteFile(index) {
      this.other_fileArray.splice(index, 1);
      var fileTarget = this.$refs.other_file;
      fileTarget.value = "";
    },

    other_changeFile() {
      var fileTarget = this.$refs.other_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.other_fileArray.indexOf(fileTarget.files[i]) == -1 ||
          this.other_fileArray.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.other_fileArray.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    other_clear() {
      this.other_remark = "";
      this.date_data_submission = "";
      
      this.other_fileArray = [];
      if(this.other_type == "2") 
        this.$refs.other_file.value = "";

      this.other_type = "2";

      this.other_canSub = true;
    },

    other_create() {
      let _this = this;
      if (this.other_remark.trim() == "") {
        Swal.fire({
          text: "Please enter Description!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.date_data_submission == "" && this.other_type == "4") {
        Swal.fire({
          text: "Column “Date” is required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("remark", this.other_remark.trim());
      form_Data.append("kind", this.other_type.trim());
      form_Data.append("date_data_submission", this.date_data_submission);

      for (var i = 0; i < this.other_fileArray.length; i++) {
        let file = this.other_fileArray[i];
        form_Data.append("files[" + i + "]", file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_client_po",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.other_clear();
          _this.getRecords();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    
    po_deleteFile(index) {
      this.po_fileArray.splice(index, 1);
      var fileTarget = this.$refs.po_file;
      fileTarget.value = "";
    },

    po_changeFile() {
      var fileTarget = this.$refs.po_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.po_fileArray.indexOf(fileTarget.files[i]) == -1 ||
          this.po_fileArray.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.po_fileArray.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    po_clear() {
      this.po_remark = "";
      this.po_type = "";
      this.po_fileArray = [];
      this.$refs.po_file.value = "";

      this.po_canSub = true;
    },

    po_create() {
      let _this = this;
      if (this.po_remark.trim() == "") {
        Swal.fire({
          text: "Please enter Description!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("remark", this.po_remark.trim());
      form_Data.append("kind", "1");

      for (var i = 0; i < this.po_fileArray.length; i++) {
        let file = this.po_fileArray[i];
        form_Data.append("files[" + i + "]", file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_client_po",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.po_clear();
          _this.getRecords();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    order_export: async function() {
      var token = localStorage.getItem("token");

      $('.mask').toggle();

      let _this = this;
    
      const params = {
        id: _this.id,
        fc: _this.fil_category,
        fs: _this.fil_status,
        fp: _this.fil_proof,
        ft: _this.fil_creator,
        fal: _this.fil_amount_lower,
        fau: _this.fil_amount_upper,
        fpl: _this.fil_payment_lower,
        fpu: _this.fil_payment_upper,
        frl: _this.fil_ar_lower,
        fru: _this.fil_ar_upper,
        fal_eq: _this.fil_amount_lower_eq,
        fau_eq: _this.fil_amount_upper_eq,
        fpl_eq: _this.fil_payment_lower_eq,
        fpu_eq: _this.fil_payment_upper_eq,
        frl_eq: _this.fil_ar_lower_eq,
        fru_eq: _this.fil_ar_upper_eq,

        aging: _this.fil_aging,

        fk: _this.fil_keyowrd,
        fkp: _this.fil_keyowrd_p,
        of1: _this.od_factor1,
        ofd1: _this.od_factor1_order,
        of2: _this.od_factor2,
        ofd2: _this.od_factor2_order,
        page: _this.page,
        size: _this.perPage,
      };

      await axios({
        method: "post",
        url: "api/quotation_payment_mgt_export",
        params,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "quotation_payment_mgt_export.xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        })
        .finally(() => {
          $('.mask').toggle();
        });
    },
  },
});
