var app = new Vue({
  el: "#app",
  data: {
    submit: false,
    sn: 0,
    sn1: 0,
    sn2: 0,
    agenda: [],
    agenda1: [],
    agenda2: [],

    position: [],
    title: [],
    department: "",
    title_id: 0,

    // info
    name :"",
    title: "",
    is_manager: "",
    uid : 0,

    // data
    type: 0,
    version: "",
    category: "",
    criterion: "",

    org_category: "",
    org_criterion: "",
    org_id: 0,
    org_type: 0,

    editing: false,

    // paging
    page: 1,
    //perPage: 5,
    pg:0,
    pages: [],
    pages_10: [],

    perPage: 10,

    receive_records: [],
    view_detail: false,
    record: {},
    proof_id: 0,

    // editing
    e_title: [],

    e_sn: 0,
    e_sn1: 0,
    e_sn2: 0,

    e_org_category: "",
    e_org_criterion: "",
    e_org_id: 0,
    e_org_type: 0,

    e_type: 0,
    e_version: "",
    e_category: "",
    e_criterion: "",

    e_department: "",

    e_tid: 0,

    e_editing: false,

    // search
    keyword: "",
    fil_status: "",

    // attributes
    topic: "",
    start_date: "",
    end_date: "",
    access: [],
    rule: "",
    display: "",
    sort: "",

    block: { 
      url: "",
      photo: "",
     },

    id:0,

    // detail attributes
    title: "",
    pic: "",
    url: "",
    link: "",
    description: "",

    blocks:[],

    details: [],

    fileArray: [],
    edit_fileArray: {},

    item:[],

    vote1: "",
    vote2: "",

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
            case "kw":
              _this.keyword = decodeURI(tmp[1]);
              break;
            case "st":
              _this.fil_status = tmp[1];
              break;
            case "pg":
              _this.pg = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }

    this.getRecords();
    this.getUserName();
    this.getAccess();
  },

  computed: {
    displayedRecord() {
    if(this.pg == 0)
        this.filter_apply();

      this.setPages();
      return this.paginate(this.receive_records);
    },
  },

  mounted() {
    
  },

  watch: {

    receive_records() {
      console.log("Vue watch receive_records");
      this.setPages();
    },
    

    proof_id() {
      this.detail();
    },
  },

  methods: {
    search() {
        this.filter_apply();
    },

    setPages() {
      console.log("setPages");
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function(posts) {
      console.log("paginate");
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
          let perPage = this.perPage;
          let from_d = (page * perPage) - perPage;
          let to_d = (page * perPage);

      let tenPages = Math.floor((this.page - 1) / 10);
      if(tenPages < 0)
        tenPages = 0;
      this.pages_10 = [];
      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      this.pages_10 = this.pages.slice(from, to);
      
      return this.receive_records.slice(from_d, to_d);
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


    filter_apply: function() {
        let _this = this;
  
        window.location.href =
          "voting_topic_mgt?" +
          "kw=" +
          _this.keyword +
          "&st=" +
          _this.fil_status +
          "&pg=" +
          _this.page;
      },

    getRecords: function() {
      let _this = this;

      const params = {
        kw: _this.keyword,
        st: _this.fil_status,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/voting_topic_mgt", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if (_this.receive_records.length > 0) {
            //_this.proof_id = _this.receive_records[0].id;
            //_this.detail();
            if(_this.pg !== 0)
            { 
              _this.page = _this.pg;
              _this.setPages();
            }
            
          }
        })
        .catch(function(error) {
          console.log(error);
        });

      _this.proof_id = 0;
    },

    view_detai:function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to view",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }else
          {
            window.jQuery(".mask").toggle();
            window.jQuery('#Modal_2').toggle();
          }
        
    },

    // is this topic voted by someone
    asyncVote: async function(template_id) {
      let _this = this;
      let token = localStorage.getItem("accessToken");
      const params = {
        id: template_id,
      };

      try {
        let res = await axios.get("api/voting_topic_mgt_vote", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.record.votes = res.data;
        // find receive_recrod's index
        var rec = this.receive_records.find((element) => element.id == template_id);

        if (rec) {
          rec.votes = res.data;
        }

      } catch (err) {
        console.log(err)
        alert('error')
      }

    },


    edit_detail: async function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to edit",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }
          
          await this.asyncVote(this.proof_id);

          if (this.record.votes.length > 0) {
            Swal.fire({
                text: "Someone has already voted in this topic, so it cannot be edited.",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }

          if(this.record.create_id != this.uid && this.vote2 != true) {
            Swal.fire({
              text: "User doesn’t have the access to execute this action.",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }

          
          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_3').toggle();
          $('#access_edit').selectpicker('refresh');
          
        
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        //this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );


    },

    create_template() {
  
      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("topic", this.topic);
      form_Data.append("start_date", this.start_date);
      form_Data.append("end_date", this.end_date);
      form_Data.append("access", JSON.stringify(this.access));
      form_Data.append("rule", this.rule);
      form_Data.append("display", this.display);
      form_Data.append("sort", this.sort);

      form_Data.append("blocks", JSON.stringify(this.blocks));

      var myArr = this.fileArray;
      myArr.forEach((element, index) => {
        var data = myArr[index];

        form_Data.append("file" + myArr[index].id, data);

      });


      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/voting_topic_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();

          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_1').toggle();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.submit = false;
        });

        
    },

    duplicate() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to duplicate",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("id", this.record.id);
      form_Data.append("topic", this.record.topic);
      form_Data.append("start_date", this.record.start_date);
      form_Data.append("end_date", this.record.end_date);
      form_Data.append("access", JSON.stringify(this.record.access_array));
      form_Data.append("rule", this.record.rule);
      form_Data.append("display", this.record.display);
      form_Data.append("sort", this.record.sort);


      form_Data.append("blocks", JSON.stringify(this.record.details));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/voting_topic_duplicate",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        });
    },

    format_url: function(url) {
      var pattern = /^((http|https|ftp):\/\/)/;

      if(!pattern.test(url)) {
          url = "https://" + url;
      }
      return url.replace(/\\/g, "/");
    },

    async remove() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to delete",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      await this.asyncVote(this.proof_id);

      if(this.record.votes.length != 0)
      {
        Swal.fire({
          text: "Someone has already voted in this topic, so it cannot be deleted. ",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      };

      if(this.record.create_id != this.uid && this.vote2 != true) {
        Swal.fire({
          text: "User doesn’t have the access to execute this action.",
          icon: "warning",
          confirmButtonText: "OK",
        });
      return;
    }

      let _this = this;

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
          if (_this.submit == true) return;

          _this.submit = true;

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("id", _this.record.id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/voting_topic_delete",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.reset();
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.reset();
            });
        } else {
          return;
        }
      });
    },

    async update_template() {

      if (this.proof_id == 0) {
      
        return;
      }

      await this.asyncVote(this.proof_id);

      if(this.record.votes.length != 0)
      {
        Swal.fire({
          text: "Someone has already voted in this topic, so it cannot be edited. ",
          icon: "warning",
          confirmButtonText: "OK",
        });

        window.jQuery(".mask").toggle();
        window.jQuery('#Modal_3').toggle();

        return;
      };

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("id", this.record.id);
      form_Data.append("topic", this.record.topic);
      form_Data.append("start_date", this.record.start_date);
      form_Data.append("end_date", this.record.end_date);
      form_Data.append("access", JSON.stringify(this.record.access_array));
      form_Data.append("rule", this.record.rule);
      form_Data.append("display", this.record.display);
      form_Data.append("sort", this.record.sort);


      form_Data.append("blocks", JSON.stringify(this.record.details));

      var myArr = this.fileArray;
      myArr.forEach((element, index) => {
        var data = myArr[index];

        form_Data.append("file" + myArr[index].id, data);

      });

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/voting_topic_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_3').toggle();

          _this.reset();
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });
          _this.submit = false;
        });

    },

    add_criterion: function() {
      if (
        this.title.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // get max id in this.blocks
      var max_id = 0;
      for (var i = 0; i < this.blocks.length; i++) {
        if (this.blocks[i].id > max_id) {
          max_id = this.blocks[i].id;
        }
      }
      var _id = max_id + 1;

      var fileTarget = this.$refs.block_image_0.files[0];
      var found = false;
      if(fileTarget) {
        for(var i = 0; i < this.fileArray.length; i++)
        {
          if(this.fileArray[i].id == this.item.id)
          {
            this.fileArray[i] = Object.assign(fileTarget, { id: _id });
            found = true;
            break;
          }
        }

        if (!found) {
          var fileItem = Object.assign(fileTarget, { id: _id });
          this.fileArray.push(fileItem);
        }
      }

      var detail = {
          id: _id,
          title: this.title,
          description: this.description,
          url: this.block.url,

          link: this.link,
        };

        this.blocks.push(detail);

        this.clear_criterion();
      },

      clear_criterion: function() {
        this.title = "";
        this.description = "";
        this.link = "";
        this.block = { 
          url: "",
          photo: "",
         };

         document.getElementById('block_image_0').value = "";
      },


      onFileChangeImage(e, item, num) {
        const file = e.target.files[0];

        if (num === 0) {
          item.url = URL.createObjectURL(file);
        }


      },

      clear_photo(item, num) {

        if (num === 0) {
          item.url = "";
          document.getElementById('block_image_' + num).value = "";
        }

      
      },

    clear_edit: function() {
      this.title = "";
      this.description = "";
      this.link = "";
      this.block = {
        url: "",
        photo: "",
      };

      this.item = [];

      this.editing = false;
    },

    cancel_criterion: function() {


      this.clear_edit();
    },

    update_criterion: function() {
      if (this.title.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var fileTarget = this.$refs.block_image_0.files[0];
      var found = false;
      if(fileTarget) {
        for(var i = 0; i < this.fileArray.length; i++)
        {
          if(this.fileArray[i].id == this.item.id)
          {
            this.fileArray[i] = Object.assign(fileTarget, { id: this.item.id });
            found = true;
            break;
          }
        }

        if (!found) {
          var fileItem = Object.assign(fileTarget, { id: this.item.id });
          this.fileArray.push(fileItem);
        }
      }

      this.item.title = this.title;
      this.item.description = this.description;
      this.item.link = this.link;
      this.item.url = this.block.url;
      
      this.clear_edit();
    },

   


    set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.blocks.find(({ id }) => id === eid);
      this.blocks.splice(fromIndex, 1);
      this.blocks.splice(toIndex, 0, element);
    },

    set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.blocks.length - 1) toIndex = this.blocks.length - 1;

      var element = this.blocks.find(({ id }) => id === eid);
      this.blocks.splice(fromIndex, 1);
      this.blocks.splice(toIndex, 0, element);
    },

    edit: function(item) {
      this.scrollMeTo('addto');

      var element = item;
      this.item = item;

      this.title = item.title;
      this.description = element.description;

      this.link = element.link;

      this.block.url = element.url;

      // clone this.fileArray into this.edit_fileArray
      this.edit_fileArray = this.fileArray.map((file) => {
        return file;
      });
    
      this.editing = true;
    },

    del: function(eid) {
      var index = this.blocks.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.blocks.splice(index, 1);
      }

      var index = this.fileArray.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.fileArray.splice(index, 1);
      }

      this.clear_edit();
    },


    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

    reset: function() {
      this.submit = false;

      this.blocks = [];
  
      this.sn = 0;

      this.fileArray = [];
      this.edit_fileArray = [];

      this.topic = "";
      this.start_date = "";
      this.end_date = "";
      this.access = [];
      this.rule = "";
      this.display = "";
      this.sort = "";
  
      this.title = "";
      this.pic = "";
      this.url = "";
      this.description = "";
      this.link = "";

      this.block = {
        url: "",
      photo: "",
      };

      this.editing = false;
      this.submit = false;

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

    // editing

    e_add_criterion: function() {
      if (
        this.title.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }
      
      // get max id in this.record.details
      var max_id = 0;
      for (var i = 0; i < this.record.details.length; i++) {
        if (this.record.details[i].id > max_id) {
          max_id = this.record.details[i].id;
        }
      }
      var _id = max_id + 1;

      var fileTarget = this.$refs.block_image_1.files[0];
      var found = false;
      if(fileTarget) {
        for(var i = 0; i < this.fileArray.length; i++)
        {
          if(this.fileArray[i].id == this.item.id)
          {
            this.fileArray[i] = Object.assign(fileTarget, { id: _id });
            found = true;
            break;
          }
        }

        if (!found) {
          var fileItem = Object.assign(fileTarget, { id: _id });
          this.fileArray.push(fileItem);
        }
      }

      var detail = {
          id: _id,
          title: this.title,
          description: this.description,
          url: this.block.url,

          link: this.link,
        };

        this.record.details.push(detail);

        this.clear_criterion();
    },

    e_cancel_criterion: function() {


      this.e_clear_edit();
    },

    e_update_criterion: function() {
      if (this.title.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var fileTarget = this.$refs.block_image_1.files[0];
      var found = false;
      if(fileTarget) {
        for(var i = 0; i < this.fileArray.length; i++)
        {
          if(this.fileArray[i].id == this.item.id)
          {
            this.fileArray[i] = Object.assign(fileTarget, { id: this.item.id });
            found = true;
            break;
          }
        }

        if (!found) {
          var fileItem = Object.assign(fileTarget, { id: this.item.id });
          this.fileArray.push(fileItem);
        }
      }

      var rec = this.record.details.find(({ id }) => id === this.item.id);

      rec.title = this.title;
      rec.description = this.description;
      rec.link = this.link;
      rec.url = this.block.url;
      
      this.e_clear_edit();
    },

    e_clear_edit: function() {
      this.title = "";
      this.description = "";
      this.link = "";
      this.block = {
        url: "",
        photo: "",
      };

      this.item = [];

      this.e_editing = false;
    },

    e_set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.details.find(({ id }) => id === eid);
      this.record.details.splice(fromIndex, 1);
      this.record.details.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.details.length - 1)
        toIndex = this.record.details.length - 1;

      var element = this.record.details.find(({ id }) => id === eid);
      this.record.details.splice(fromIndex, 1);
      this.record.details.splice(toIndex, 0, element);
    },

    e_edit: function(item) {
      this.scrollMeTo('porto');
      var element = item;
      this.item = item;

      this.title = item.title;
      this.description = element.description;

      this.link = element.link;

      this.block.url = element.url;

      // clone this.fileArray into this.edit_fileArray
      this.edit_fileArray = this.fileArray.map((file) => {
        return file;
      });

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.record.details.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.details.splice(index, 1);
      }
    },

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
          url: 'api/voting_topic_mgt_access_control',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.vote1 = response.data.vote1;
          _this.vote2 = response.data.vote2;

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

    getUserName: function() {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);

      axios({
          method: 'post',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/on_duty_get_myname',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.name = response.data.username;
          _this.uid = response.data.user_id;

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

  },
});
