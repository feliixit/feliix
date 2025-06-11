var app = new Vue({
  el: "#app",
  data: {
    name: "",
    month1: "",

    picked: "A",
    view_detail: false,

    submit: false,

    new_info_account:'',
    new_info_category:'',
    new_info_sub_category:'',

    new_info_remark:'',
    new_info_remark_other:'',

    receive_records: [],
    record: {},

    baseURL: "https://storage.googleapis.com/feliiximg/",

    proof_remark: "",
    reject_reason: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    perPage: 10000,

    is_approval: false,
  },

  created() {
    this.getUserName();
  },

  computed: {
    displayedRecord() {
      this.setPages();
      return this.paginate(this.receive_records);
    },

    wrongNumber: function () {
      return this.isNumeric(this.record.total) === false
    },
    
  },

  mounted() {
    var d1 = new Date();
    this.month1 = d1;

    $("#start").val(
      d1
        .toISOString()
        .slice(0, 7)
        .replace(/-/g, "-")
    );

    this.getLeaveCredit();
  },

  watch: {
    picked() {
      this.getLeaveCredit();
    },

    proof_id() {
        this.detail();
    }
    
  },

  methods: {
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
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.receive_records.slice(from, to);
    },

    isNumeric: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    getLeaveCredit: function() {
      let _this = this;

      axios
        .get("api/expense_releasing")
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if(_this.receive_records.length > 0)
          {
            
            //_this.proof_id = 0;
              //_this.proof_id = _this.receive_records[0].id;
              //_this.detail();
          }
        })
        .catch(function(error) {
          console.log(error);
        });

        _this.proof_id = 0;
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

    export_petty: function() {
   
      let _this = this;
      var form_Data = new FormData();

      form_Data.append('id', this.record["id"])
     
      const filename = "leave";

      const token = sessionStorage.getItem('token');

      axios({
              method: 'post',
              url: 'expense_release_application',
              data: form_Data,
              responseType: 'blob', // important
          })
          .then(function(response) {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
               
                  link.setAttribute('download', 'Expense Application Voucher_' + _this.record['request_no'] + '.docx');
               
                document.body.appendChild(link);
                link.click();

          })
          .catch(function(response) {
              //handle error
              console.log(response)
          });
  },

    approveReceiveRecord_OP: function(id) {

      if(this.submit == true) return;

      this.submit = true;

      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Releasing");
      form_Data.append("id", id);
      form_Data.append("remark", this.reject_reason);
   
      for( var i = 0; i < this.$refs.file.files.length; i++ ){
        let file = this.$refs.file.files[i];
        form_Data.append('files[' + i + ']', file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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

          _this.resetForm();
        });
    },

    confirm_change_account: function() {
      let _this = this;

      let OrgAccount = this.record.info_account;
      
      let OrgCategory = this.record.info_category;
      let OrgSubCategory = this.record.sub_category;

      let OrgRemark = this.record.info_remark;
      let OrgRemarkOther = this.record.info_remark_other;

      

      if(OrgAccount !== this.new_info_account || OrgCategory !== this.new_info_category || OrgSubCategory !== this.new_info_sub_category || OrgRemark !== this.new_info_remark || OrgRemarkOther !== this.new_info_remark_other)
      {
        Swal.fire({
          title: "Are you sure?",
          text: "You are changing the additional info?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            _this.change_additional_info(OrgAccount, OrgCategory, OrgSubCategory, OrgRemark, OrgRemarkOther);
          }
        });
      }
      
    },

    change_additional_info: function(OrgAccount, OrgCategory, OrgSubCategory, OrgRemark, OrgRemarkOther) {
      let _this = this;
      targetId = this.record.id;

      var form_Data = new FormData();

      if(this.new_info_remark != 'Other')
        this.new_info_remark_other = '';

        if(this.new_info_category !== 'Marketing' && this.new_info_category !== 'Office Needs' && this.new_info_category !== 'Others' && this.new_info_category !== 'Projects' && this.new_info_category !== 'Store')  
        this.new_info_sub_category = '';

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("id", targetId);
      form_Data.append("org_info_account", OrgAccount);
      form_Data.append("new_info_account", this.new_info_account);

      form_Data.append("org_info_category", OrgCategory);
      form_Data.append("new_info_category", this.new_info_category);
      form_Data.append("org_sub_category", OrgSubCategory);
      form_Data.append("new_sub_category", this.new_info_sub_category);

      form_Data.append("org_info_remark", OrgRemark);
      form_Data.append("new_info_remark", this.new_info_remark);
      form_Data.append("org_info_remark_other", OrgRemarkOther);
      form_Data.append("new_info_remark_other", this.new_info_remark_other);
      
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_change_category",
        data: form_Data,
      })
        .then(function(response) {
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
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        });
      },

    change_account: function() {
      let _this = this;
      targetId = this.record.id;
      OrgAccount = this.record.info_account;

      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("id", targetId);
      form_Data.append("org_info_account", OrgAccount);
      form_Data.append("new_info_account", this.new_info_account);
      
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_change_account",
        data: form_Data,
      })
        .then(function(response) {
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
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        });
      },
    
      
    approveReceiveRecord_PCR: function(id) {

      if(this.submit == true) return;
      this.submit = true;

      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Finish Releasing PCR");
      form_Data.append("id", id);
      form_Data.append("remark", "");

      for( var i = 0; i < this.$refs.file.files.length; i++ ){
        let file = this.$refs.file.files[i];
        form_Data.append('files[' + i + ']', file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        });
    },


    approveReceiveRecord_MD: function(id) {

      if(this.submit == true) return;
      this.submit = true;

      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Finish Releasing");
      form_Data.append("id", id);
      form_Data.append("remark", "");

      for( var i = 0; i < this.$refs.file.files.length; i++ ){
        let file = this.$refs.file.files[i];
        form_Data.append('files[' + i + ']', file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        });
    },

    rejectReceiveRecord_Checker: function(id) {
      if(this.submit == true) return;

      this.submit = true;

        let _this = this;
        targetId = this.record.id;
        var form_Data = new FormData();
  
        var token = localStorage.getItem("token");
        form_Data.append("jwt", token);
  
        form_Data.append("crud", "Void");
        form_Data.append("id", id);
        form_Data.append("remark", this.reject_reason);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
          url: "api/petty_cash_action",
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
              icon: "info",
              confirmButtonText: "OK",
            });

            _this.resetForm();
          });
      },

    rejectReceiveRecord: function(id) {

      if(this.submit == true) return;

      this.submit = true;

      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Void");
      form_Data.append("id", id);
      form_Data.append("remark", this.reject_reason);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.resetForm();
        });
    },

    get_lastest_record_status : async function(id) {
      // get api/expense_status
      let status = await axios.get("api/expense_status", {
        params: {
          id: id
        }
      });

      return status.data;
    },

    detail: async function() {
      let _this = this;

      //let favorite = [];

      //for (i = 0; i < this.receive_records.length; i++)
      //{
      //  if(this.receive_records[i].is_checked == 1)
      //    favorite.push(this.receive_records[i].sid);
      //}

      if (this.proof_id == 0) {
        // Swal.fire({
        //   text: "Please select row to see the detail!",
        //   icon: "warning",
        //   confirmButtonText: "OK",
        // });

        //$(window).scrollTop(0);
        this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 5)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Release". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      this.new_info_account = this.record.info_account;
      this.new_info_category = this.record.info_category;
      this.new_info_sub_category = (this.record.sub_category == undefined ? '' : this.record.sub_category);
      this.new_info_remark = this.record.info_remark;
      this.new_info_remark_other = this.record.info_remark_other;
      
      this.reject_reason = "";
      this.view_detail = true;
    },

    account_privileges: function() {

      if(this.record.info_account === 'Office Petty Cash' && (this.name.trim() === 'Mary Jude Jeng Articulo' || this.name.trim() === 'Dennis Lin' || this.name.trim() === 'Marie Kayla Patricia Dequina' || this.name.trim() === "Stephanie De dios"))
        return true;
      
      if(this.record.info_account === 'Online Transactions' && (this.name.trim() === 'Mary Jude Jeng Articulo' || this.name.trim() === 'Dennis Lin' || this.name.trim() === 'Marie Kayla Patricia Dequina' || this.name.trim() === "Stephanie De dios"))
        return true;

      if(this.record.info_account === 'Security Bank' && (this.name.trim() === 'Glendon Wendell Co' || this.name.trim() === 'Dennis Lin'))
        return true;

      if(this.record.info_account === 'Security Bank => Office Petty Cash' && (this.name.trim() === 'Glendon Wendell Co' || this.name.trim() === 'Dennis Lin'))
        return true;

      return false;
    },

    approve_op: async function() {
      let _this = this;

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 5)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Release". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      // check account privileges
      if(this.account_privileges() === false)
      {
       
          Swal.fire({
            text: "Invalid Releaser",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        
      }
      

      if (!this.$refs.file.files[0])
          {
            Swal.fire({
              text: 'File Attachment required',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Location Photo required';
            //$(window).scrollTop(0);
   
            return false;
          }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Finish releasing and then go to the liquidating step",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.approveReceiveRecord_OP(this.proof_id);
        }
      });
    },

    approve_md: async function() {
      let _this = this;

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 5)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Release". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      // check account privileges
      if(this.account_privileges() === false)
      {
       
          Swal.fire({
            text: "Invalid Releaser",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        
      }

      if (!this.$refs.file.files[0])
        {
          Swal.fire({
            text: 'File Attachment required',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          //this.err_msg = 'Location Photo required';
          //$(window).scrollTop(0);
          return false;
        }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Finish releasing and the status of this application will become completed",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
  
          _this.approveReceiveRecord_MD(this.proof_id);


        }
      });
    },

    
    approve_pcr: async function() {
      let _this = this;

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 5)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Release". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      // check account privileges
      if(this.account_privileges() === false)
      {
       
          Swal.fire({
            text: "Invalid Releaser",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        
      }

      if (!this.$refs.file.files[0])
        {
          Swal.fire({
            text: 'File Attachment required',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          //this.err_msg = 'Location Photo required';
          //$(window).scrollTop(0);
          return false;
        }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Finish releasing and the status of this application will become completed",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
  
          _this.approveReceiveRecord_PCR(this.proof_id);


        }
      });
    },

    reject: function() {
      let _this = this;

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be rejected!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.reject_reason.trim() === "") {
        Swal.fire({
          text: "Please enter reject reason!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Void Application and send back to requestor",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          _this.rejectReceiveRecord(this.proof_id);

        }
      });
    },

    reject_checker: function() {
        let _this = this;
  
        if (this.proof_id < 1) {
          Swal.fire({
            text: "Please select applicant to be rejected!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if (this.reject_reason.trim() === "") {
          Swal.fire({
            text: "Please enter reject reason!",
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

            _this.rejectReceiveRecord_Checker(this.proof_id);
  
          }
        });
      },
   

    resetForm: function() {
      this.record = [];
      this.reject_reason = "";
      this.getLeaveCredit();
      this.submit = false;
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },
  },
});
