var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    employee: "",
    employees: [],

    templates: [],
    template: "",

    month_type : 0,

    review_month: "",
    review_next_month: "",

    review_next_month3: "",

    review_month_1:"",

    sdate: "2021-01",
    edate: "",

    editing: false,
    pid : 0,

    // paging
    page: 1,
    //perPage: 5,
    pg: 0,
    pages: [],
    pages_10: [],

    perPage: 10,

    receive_records: [],
    view_detail: false,
    record: {},
    proof_id: 0,


    // evaluate
    evals:{},
    avg:10.0,
    avg1:10.0,
    avg2:10.0,

    comment1:"",
    comment2:"",
    comment3:"",
    comment4:"",
    comment5:"",
    comment6:"",

    // I&AM
    department:'',
    title: '',
    username:'',
    user_id:'',
    email:'',

    // view
    views:{},
    emp_avg:10.0,
    mag_avg:10.0,
    emp_avg1:10.0,
    mag_avg1:10.0,
    emp_avg2:10.0,
    mag_avg2:10.0,

    // search
    keyword: "",

    anwser: "",
    access: [],

    username: "",
    username1: "",
    username2: "",

    date_created: "",
    date_finished: "",
    leadership_assessment: false,
    direct_access:[],
    manager_access:[],
    peer_access:[],
    other_access:[],
    outsider_name1:"",
    outsider_email1:"",
    outsider_name2:"",
    outsider_email2:"",

    review: {},
    period : 0,
    question: [],
    answers : [],
    review_answers: [],

    section : '',
    section_answers: [],
    section_answers_comment1: [],
    section_answers_comment2: [],
    
    overall_avg : 0,
    direct_report : 0,
    manager : 0,
    peer : 0,
    other : 0,
    self : 0,

    chart: null,

    other1 : "",
    other2 : "",
  },

  created() {
    let _this = this;
    let uri = window.location.href.split("?");;

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
            case "id":
              _this.pid = decodeURI(tmp[1]);
              break;
            case "sdate":
              _this.sdate = decodeURI(tmp[1]);
              break;
            case "edate":
              _this.edate = decodeURI(tmp[1]);
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

    this.getEmployees();
    this.getUserName();
    this.getLeaveCredit();
    this.getLeadershipAssessmentControl();

    
  },

  computed: {
    displayedRecord() {
      if (this.pg == 0) this.filter_apply();

      this.setPages();
      return this.paginate(this.receive_records);
    },
  },

  mounted() {},

  watch: {
    // employee() {
    //   this.getTemplatesByTitle();
    // },
    
    receive_records() {
      console.log("Vue watch receive_records");
      this.setPages();
    },

    review_month(d) {
      var d = new Date(d + '-01');
      var d_next = new Date(d.setMonth(d.getMonth() + 1));
      this.review_next_month = moment(d_next).format('YYYY-MM');
      var d_next = new Date(d.setMonth(d.getMonth() + 1));
      this.review_next_month3 = moment(d_next).format('YYYY-MM');
    },

  },

  methods: {
    set_comment: async function(section) {
      
      await this.getLeadershipAssessmentComment(section);

      this.section = section;

    },

    set_appendix: async function(section) {
      
      await this.getLeadershipAssessmentAnswer(section);

      this.section = section;

    },

    set_section: async function(section) {
      
      await this.getLeadershipAssessmentAnswer(section);

      this.section = section;

      setTimeout(() => {
        if(section == 'PRODUCTION')
          this.load_chart('chart1', 'PRODUCTION SCORES *', '65, 144, 76', '#41904c');
        if(section == 'PERMISSION')
          this.load_chart('chart2', 'PERMISSION SCORES *', '222, 186, 64', '#dfba40');
        if(section == 'PINNACLE-SELF')
          this.load_chart('chart3', 'PINNACLE-SELF SCORES *', '40, 66, 148', '#284294');
        if(section == 'PINNACLE-OTHERS')
          this.load_chart('chart4', 'PINNACLE-OTHERS SCORES *', '40, 107, 236', '#286bec');
        if(section == 'POSITION')
          this.load_chart('chart5', 'POSITION SCORES *', '177, 44, 40', '#b02c28');
        if(section == 'PEOPLE DEVELOPMENT')
          this.load_chart('chart6', 'DEVELOPS OTHERS SCORES *', '95, 50, 139', '#5f328b');
      }, 500);
      
    },

    load_chart( chart_id, title, main_color, title_color) {
      var labels = ['Overall', 'Direct Reports', 'Manager', 'Peer', 'Other', 'Self'];
      var overall = 0;
      var direct = 0;
      var manager = 0;
      var peer = 0;
      var other = 0;
      var self = 0;
      
      for(var i = 0; i < this.section_answers.length; i++)
      {
        overall += parseFloat(this.section_answers[i].average);
        direct += parseFloat(this.section_answers[i].direct);
        manager += parseFloat(this.section_answers[i].manager);
        peer += parseFloat(this.section_answers[i].peer);
        other += parseFloat(this.section_answers[i].other);
        self += parseFloat(this.section_answers[i].self);
      }

      overall = (overall / this.section_answers.length).toFixed(1);
      direct = (direct / this.section_answers.length).toFixed(1);
      manager = (manager / this.section_answers.length).toFixed(1);
      peer = (peer / this.section_answers.length).toFixed(1);
      other = (other / this.section_answers.length).toFixed(1);
      self = (self / this.section_answers.length).toFixed(1);

      this.overall_avg = overall;
      this.direct_report = direct;
      this.manager = manager;
      this.peer = peer;
      this.other = other;
      this.self = self;

      var data = {
        labels: labels,
        datasets: [{
          data: [overall, direct, manager, peer, other, self],
          backgroundColor: [
            'rgba('+ main_color + ', 0.8)',
            'rgba(148, 148, 148, 0.8)',
            'rgba(148, 148, 148, 0.8)',
            'rgba(148, 148, 148, 0.8)',
            'rgba(148, 148, 148, 0.8)',
            'rgba(57, 35, 107, 0.8)'
          ],
          borderColor: [
            'rgb('+ main_color + ')',
            'rgb(148, 148, 148)',
            'rgb(148, 148, 148)',
            'rgb(148, 148, 148)',
            'rgb(148, 148, 148)',
            'rgb(57, 35, 107)'
          ],
          borderWidth: 1
        }]
      }

      if(this.chart != null)
        this.chart.destroy();
     

      var new_chart = new Chart(document.getElementById(chart_id).getContext('2d'), {
        type: 'bar',
        labels: labels,
        data: data,
        options: {
            indexAxis: 'y',
            elements: {
                bar: {
                    borderWidth: 2,
                }
            },
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        top: 10,
                        bottom: 30
                    },
                    font : {
                      size: 32,
                    },
                    color : title_color
                },
              
                legend: {
                  display: false,
                  labels: {
                    fontSize: 20,
                    fontColor: '#595d6e',
                  }
                },
                tooltips: {
                  enabled: false
                },
            },
            scales: {
              x: {
                min: 0,
                max: 7
              },
              
          },
          
        },
    });

      this.chart = new_chart;
  },

    getLeadershipAssessmentAnswer: function(section) {
      let _this = this;
      var token = localStorage.getItem("token");

      params = {
        id : _this.proof_id,
        section: section,
      };
      
      axios({
        method: "get",
        params,
        headers: { Authorization: `Bearer ${token}` },
        url: "api/leadership_assessment_answer",
      })
        .then(function(response) {
          console.log(response.data);
          _this.section_answers = response.data;
        }
        )
        .catch(function(error) {
          console.log(error);
        }
        );
    },

    getLeadershipAssessmentComment: function() {
      let _this = this;
      var token = localStorage.getItem("token");

      params = {
        id : _this.proof_id,
      };
      
      axios({
        method: "get",
        params,
        headers: { Authorization: `Bearer ${token}` },
        url: "api/leadership_assessment_answer_comment",
      })
        .then(function(response) {
          console.log(response.data);
          _this.section_answers_comment1 = response.data.comment1;
          _this.section_answers_comment2 = response.data.comment2;
        }
        )
        .catch(function(error) {
          console.log(error);
        }
        );
    },

    complete_answer: async function(period) {
      let _this = this;

      // if(this.comment1.trim() == "" || this.comment2.trim() == "" || this.comment3.trim() == "")
      // {
      //   Swal.fire({
      //     text: "Please give the rating for each question.",
      //     icon: "warning",
      //     confirmButtonText: "OK",
      //   });

      //   return;
      // }

      if(_this.check_comment_size(2048) != 0) 
        {
          Swal.fire({
            text: "Text length cannot exceed " + max_length + " characters.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

      var id = "comment1";
      for (var key in this.answers) {
        if(key == id)
        {
          this.answers[key] = this.comment1;
          break;
        }
      }

      id = "comment2";
      for (var key in this.answers) {
        if(key == id)
        {
          this.answers[key] = this.comment2;
          break;
        }
      }

      id = "comment3";
      for (var key in this.answers) {
        if(key == id)
        {
          this.answers[key] = this.comment3;
          break;
        }
      }

      Swal.fire({
        text: "Are you sure to submit survey? Once you submit the survey, the survey becomes completed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", this.review.id);
          form_Data.append("record_id", this.review.pid);
          form_Data.append("period", parseInt(period) + 1);
          form_Data.append("access_type", _this.record.access_type);
          form_Data.append("answer", JSON.stringify(_this.answers));

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/leadership_assessment_review_complete",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.getLeadershipAssessmentReview();
              _this.get_questions(parseInt(period) - 2);

              window.jQuery(".mask").toggle();
              window.jQuery("#Modal_3").toggle();

              _this.getLeaveCredit();

              _this.comment1 = "";
              _this.comment2 = "";
              _this.comment3 = "";

            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });


              //_this.reset();
            });

            
        } else {
          return;
        }
      });
      
      },

    save_answer: async function(period) {
      let _this = this;
      this.answers = JSON.parse(this.review.answer);

      for(var i = 0; i < this.question.length; i++)
      {
        var grade = "";
        var elements = document.getElementsByName("question_" + this.question[i].id);
        for (var j = 0 ; j < elements.length; j++)
        {
            if (elements[j].checked)
            {
              grade = elements[j].value;
              break;
            }
        }

        if(grade == "")
        {
          Swal.fire({
            text: "Please give the rating for each question.",
            icon: "warning",
            confirmButtonText: "OK",
          });

          return;
        }

        var id = "answer" + this.question[i].id;

        for (var key in this.answers) {
          if(key == id)
          {
            this.answers[key] = grade;
            break;
          }
        }
      }

      Swal.fire({
        text: "Are you sure to go to the next page? Once you go to the next page, you are not allowed to change your ratings on this page.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {



          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", this.review.id);
          form_Data.append("period", parseInt(period) + 1);
          form_Data.append("answer", JSON.stringify(_this.answers));

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/leadership_assessment_review_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.getLeadershipAssessmentReview();
              _this.get_questions(parseInt(period) - 2);

            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });


              //_this.reset();
            });

            
        } else {
          return;
        }
      });
      
      },

    to_save_answer: function(period) {
      console.log("to_save_answer");
    },
    
    save_respondent() {
      
      let _this = this;
      var len = 0;
      var emails = [];

      if(this.direct_access.length != 2 || this.manager_access.length != 2 || this.peer_access.length != 2)
      {
        Swal.fire({
          text: "Please choose exactly two respondents for each category.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // length of other_access + outsider_email1 + outsider_email2 need to be 2
      if(this.outsider_email1.trim() != '')
      {
        emails.push(this.outsider_email1.trim().toLowerCase());
        len += 1;
      }
      if(this.outsider_email2.trim() != '')
      {
        emails.push(this.outsider_email2.trim().toLowerCase());
        len += 1;
      }

      if(this.other_access.length + len != 2)
      {
        Swal.fire({
          text: "Please choose exactly two respondents for each category.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      for(var i = 0; i < this.direct_access.length; i++)
      {
        var email = this.employees.find((element) => element.username == this.direct_access[i]);
        if(email != undefined)
          emails.push(email.email.trim().toLowerCase());
      }
      for(var i = 0; i < this.manager_access.length; i++)
      {
        var email = this.employees.find((element) => element.username == this.manager_access[i]);
        if(email != undefined)
          emails.push(email.email.trim().toLowerCase());
      }
      for(var i = 0; i < this.peer_access.length; i++)
      {
        var email = this.employees.find((element) => element.username == this.peer_access[i]);
        if(email != undefined)
          emails.push(email.email.trim().toLowerCase());
      }
      for(var i = 0; i < this.other_access.length; i++)
      {
        var email = this.employees.find((element) => element.username == this.other_access[i]);
        if(email != undefined)
          emails.push(email.email.trim().toLowerCase());
      }
      
      // check duplicate email
      var unique = emails.filter(function(elem, index, self) {
        return index === self.indexOf(elem);
      });

      if (emails.length != unique.length) {
        Swal.fire({
          text: "Please choose completely different people to be respondents.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (_this.submit == true) return;

      _this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      form_Data.append("jwt", token);
      form_Data.append("pid", _this.proof_id);

      form_Data.append("direct_access", JSON.stringify(_this.direct_access));
      form_Data.append("manager_access", JSON.stringify(_this.manager_access));
      form_Data.append("peer_access", JSON.stringify(_this.peer_access));
      form_Data.append("other_access", JSON.stringify(_this.other_access));
      form_Data.append("outsider_name1", _this.outsider_name1);
      form_Data.append("outsider_email1", _this.outsider_email1);
      form_Data.append("outsider_name2", _this.outsider_name2);
      form_Data.append("outsider_email2", _this.outsider_email2);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/leadership_assessment_respondent_save",
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
          window.jQuery("#Modal_2").toggle();

        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.submit = false;

          //_this.reset();
        });

    },

    search(pg) {
      this.filter_apply(pg);
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
          _this.user_id = response.data.user_id;
          _this.email = response.data.email;
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

    filter_apply: function(pg) {
      let _this = this;

      if(pg != undefined) {
        _this.page = pg;
      }

      if(this.edate == '')
      {
        var today = new Date();
        var mm = ("0" + (today.getMonth() + 1)).slice(-2);
        var yyyy = today.getFullYear();
        d = yyyy + '-' + mm;
        this.edate = d;
      }

      window.location.href =
        "leadership_assessment?" + "sdate=" + _this.sdate + "&edate=" + _this.edate + "&kw=" + _this.keyword + "&id=" + _this.pid + "&pg=" + _this.page;
    },

    on_grade_change:function(event) {
      console.log(event.target.value);
      var grade = this.$refs.grade;

      var score =0.0;
      var cnt = 0;
      for (i = 0; i < grade.length; i++) {
        if(grade[i].value == -1)
          score += 0;
        else
        {
          score += parseInt(grade[i].value);
          cnt += 1;
        }
      }

      if(cnt === 0)
        this.avg = 0;
      else
        this.avg = (score / cnt).toFixed(1);

    },

    on_grade1_change:function(event) {
      console.log(event.target.value);
      var grade = this.$refs.grade1;

      var score =0.0;
      var cnt = 0;
      for (i = 0; i < grade.length; i++) {
        if(grade[i].value == -1)
          score += 0;
        else
        {
          score += parseInt(grade[i].value);
          cnt += 1;
        }
      }

      if(cnt === 0)
        this.avg1 = 0;
      else
        this.avg1 = (score / cnt).toFixed(1);

    },

    on_grade2_change:function(event) {
      console.log(event.target.value);
      var grade = this.$refs.grade2;

      var score =0.0;
      var cnt = 0;
      for (i = 0; i < grade.length; i++) {
        if(grade[i].value == -1)
          score += 0;
        else
        {
          score += parseInt(grade[i].value);
          cnt += 1;
        }
      }

      if(cnt === 0)
        this.avg2 = 0;
      else
        this.avg2 = (score / cnt).toFixed(1);

    },

    open_review: function() {
      if(this.is_add_review_privilege())
      {
        window.jQuery(".mask").toggle();
        window.jQuery("#Modal_1").toggle();
      }
    },


    async getLeadershipAssessmentReview() {
      let _this = this;
      var token = localStorage.getItem("token");

      var params = {
        pid: _this.proof_id,
        user_id: _this.user_id,
        email: _this.email,
      };
   

      var res = await axios({
        method: "get",
        headers: { Authorization: `Bearer ${token}` },
        params,
        url: "api/leadership_assessment_review",
 
      })

      this.review = res.data[0];
      this.answers = JSON.parse(this.review.answer);
      this.period = res.data[0].period;
    },

    to_next: async function(to_period) {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);

      form_Data.append("pid", this.review.id);
      // form_Data.append("block", JSON.stringify(temp_block));
      form_Data.append("period", to_period);

        let res = await axios({
          method: 'post',
          url: 'api/leadership_assessment_review_next',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        await _this.getLeadershipAssessmentReview();
        if(to_period > 3)
          await this.get_questions(to_period - 3);

    },

     get_questions: async function(step) {
      let _this = this;
      var token = localStorage.getItem("token");

      var params = {
        step: step,
      };
   

      var res = await axios({
        method: "get",
        headers: { Authorization: `Bearer ${token}` },
        params,
        url: "api/leadership_assessment_questions",
 
      })

      this.question = res.data;

      var el = document.querySelectorAll('input');
  
      for (var i = 0, n=el.length; i < n; i++){
          if (el[i].name.indexOf('question_')==0) {
            el[i].checked = false; 
          }
        }
      
    },
      

    async exeute_step() {
      let _this = this;
      await _this.getLeadershipAssessmentReview();

      if(this.period > 12)
      {
        Swal.fire({
          text: "You already filled out and submitted the survey.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.period > 3)
        await this.get_questions(this.period - 3);

      window.jQuery(".mask").toggle();
      window.jQuery("#Modal_3").toggle();

    },

    execute: function() {

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.outsider_name1 = this.record.outsider_name1;
        this.outsider_email1 = this.record.outsider_email1;
        this.outsider_name2 = this.record.outsider_name2;
        this.outsider_email2 = this.record.outsider_email2;
        

        this.direct_access = JSON.parse(this.record.direct_access);
        this.manager_access = JSON.parse(this.record.manager_access);
        this.peer_access = JSON.parse(this.record.peer_access);
        this.other_access = JSON.parse(this.record.other_access);

        var is_respondent = false;

        for(var i = 0; i < this.direct_access.length; i++)
        {
          var email = this.employees.find((element) => element.username == this.direct_access[i]);
          if(email != undefined)
            if(this.email.trim().toLowerCase() == email.email.trim().toLowerCase())
              is_respondent = true;
        }
        for(var i = 0; i < this.manager_access.length; i++)
        {
          var email = this.employees.find((element) => element.username == this.manager_access[i]);
          if(email != undefined)
            if(this.email.trim().toLowerCase() == email.email.trim().toLowerCase())
              is_respondent = true;
        }
        for(var i = 0; i < this.peer_access.length; i++)
        {
          var email = this.employees.find((element) => element.username == this.peer_access[i]);
          if(email != undefined)
            if(this.email.trim().toLowerCase() == email.email.trim().toLowerCase())
              is_respondent = true;
        }
        for(var i = 0; i < this.other_access.length; i++)
        {
          var email = this.employees.find((element) => element.username == this.other_access[i]);
          if(email != undefined)
            if(this.email.trim().toLowerCase() == email.email.trim().toLowerCase())
              is_respondent = true;
        }
        
        if(this.outsider_email1.trim().toLowerCase() == this.email.trim().toLowerCase() || this.outsider_email2.trim().toLowerCase() == this.email.trim().toLowerCase())
          is_respondent = true;
      
        if(this.record.user_id == this.user_id)
          is_respondent = true;
        
      if(this.record.status == '1' && is_respondent == false)
      {
        Swal.fire({
          text: "Only assessed employee and chosen respondents are allowed to fill out survey of this leadership assessment record.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }
      else if(this.record.status == '1' && is_respondent == true)
      {
        this.exeute_step();
      }
      else if(this.record.status == '0' && this.user_id != this.record.user_id)
      {
        Swal.fire({
          text: "Only assessed employee is allowed to choose respondent for his/her leadership assessment.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }
      else if(this.record.status == '2')
      {
        Swal.fire({
          text: "This leadership assessment record already completed. Action of “Execute” is not allowed and no need.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }
      else
      {

        window.jQuery(".mask").toggle();
        window.jQuery("#Modal_2").toggle();

        setTimeout(() => {
          $("#direct").selectpicker("refresh");
          $("#manager").selectpicker("refresh");
          $("#peer").selectpicker("refresh");
          $("#other").selectpicker("refresh");
        }, 500);

        

      }
    },

    add_review: function() {

      if (
        this.employee.username == undefined || this.employee == '' ) {
        Swal.fire({
          text: "Please choose one employee to be assessed.",
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
      form_Data.append("user_id", this.employee.id);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/leadership_assessment_insert",
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

      window.jQuery(".mask").toggle();
      window.jQuery("#Modal_1").toggle();
    },

    check_comment_size(max_length) {
      var checked = 0;

      if(this.comment1.length > max_length)
        checked = this.comment1.length;
      if(this.comment2.length > max_length)
        checked = this.comment2.length;
      if(this.comment3.length > max_length)
        checked = this.comment3.length;
   
        return checked;

    },

    review_comment_submit() {

      let _this = this;

      var max_length = 512;
      if(_this.record[0].user_id == _this.user_id)
        max_length = 512;
      if(_this.record[0].create_id == _this.user_id)
        max_length = 2048;

      if(this.comment6.length > max_length)
      {
        Swal.fire({
          text: "Text length cannot exceed " + max_length + " characters.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      Swal.fire({
        title: "Submit",
        text: "Are you sure to submit? Once submitted, you cannot revise the evaluation result anymore.",
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
          form_Data.append("pid", _this.proof_id);

          form_Data.append("commet6", _this.comment6);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/leadership_assessment_update_comment",
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
              window.jQuery("#Modal_4").toggle();

            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.submit = false;

              //_this.reset();
            });

            
        } else {
          return;
        }
      });
    },

    review_submit() {

      let _this = this;

      var max_length = 512;
      if(_this.record[0].user_id == _this.user_id)
        max_length = 512;
      if(_this.record[0].create_id == _this.user_id)
        max_length = 2048;

      
      if(_this.check_comment_size(max_length) != 0) 
      {
        Swal.fire({
          text: "Text length cannot exceed " + max_length + " characters.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      Swal.fire({
        title: "Submit",
        text: "Are you sure to submit? Once submitted, you cannot revise the evaluation result anymore.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          if (_this.submit == true) return;

          _this.submit = true;

          var qid = _this.$refs.qid;
          var opt = _this.$refs.opt;

          var qid1 = _this.$refs.qid1;
          var opt1 = _this.$refs.opt1;

          var qid2 = _this.$refs.qid2;
          var opt2 = _this.$refs.opt2;

          var grade = _this.$refs.grade;
          var grade1 = _this.$refs.grade1;
          var grade2 = _this.$refs.grade2;

          let temp = [];
          for(var i=0; i<grade.length; i++) {
            var obj = {
                id: qid[i].value,
                grade: grade[i].value,
                opt: opt[i].value
            };
            temp.push(obj);
          }

          for(var i=0; i<grade1.length; i++) {
            var obj = {
                id: qid1[i].value,
                grade: grade1[i].value,
                opt: opt1[i].value
            };
            temp.push(obj);
          }

          if(grade2 !== undefined) {
            for(var i=0; i<grade2.length; i++) {
              var obj = {
                  id: qid2[i].value,
                  grade: grade2[i].value,
                  opt: opt2[i].value
              };
              temp.push(obj);
            }
          }

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", _this.proof_id);
          form_Data.append("answers", JSON.stringify(temp));
          form_Data.append("commet1", _this.comment1);
          form_Data.append("commet2", _this.comment2);
          form_Data.append("commet3", _this.comment3);
          form_Data.append("commet4", _this.comment4);
          form_Data.append("commet5", _this.comment5);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/performance_evaluate_insert",
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
              window.jQuery("#Modal_2").toggle();

            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.submit = false;

              //_this.reset();
            });

            
        } else {
          return;
        }
      });
    },


    getTemplatesByTitle: function() {
      let _this = this;

      const params = {
        title: _this.employee.title_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_get_template_by_title", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.templates = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });

      _this.proof_id = 0;
    },

    getLeaveCredit: function() {
      let _this = this;

      var params = {
          kw: _this.keyword,
          sdate: _this.sdate,
          edate: _this.edate,
          id: _this.pid,
        };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/leadership_assessment", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if (_this.receive_records.length > 0) {
            //_this.proof_id = _this.receive_records[0].id;
            //_this.detail();
            if (_this.pg !== 0) {
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

    view: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to view",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } 

      var record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      
      if(record.status == 0)
      {
        Swal.fire({
          text: "No any result until now.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      if(record.status == 1 && record.user_id != this.user_id && this.leadership_assessment == false)
      {
        Swal.fire({
          text: "Due to confidential reason, only the assessed employee is allowed to view the survey result.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      if(record.status == 2 && record.user_id != this.user_id && this.leadership_assessment == false)
        {
          Swal.fire({
            text: "Only assessed employee and chosen respondents are allowed to view the result of this leadership assessment record.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }


      let _this = this;
      let _window = window;

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.outsider_name1 = this.record.outsider_name1;
      this.outsider_email1 = this.record.outsider_email1;
      this.outsider_name2 = this.record.outsider_name2;
      this.outsider_email2 = this.record.outsider_email2;

      this.direct_access = JSON.parse(this.record.direct_access);
      this.manager_access = JSON.parse(this.record.manager_access);
      this.peer_access = JSON.parse(this.record.peer_access);
      this.other_access = JSON.parse(this.record.other_access);

      this.other1 = this.other_access[0];
      this.other2 = this.other_access[1];

      if(this.outsider_name1 != '')
      {
        if(this.other1 == '')
          this.other1 = this.outsider_name1;
        else
          this.other2 = this.outsider_name1;
      }

      if(this.outsider_name2 != '')
        this.other2 = this.outsider_name2;

      // const params = {
      //   id: this.proof_id,
      // };

      // let token = localStorage.getItem("accessToken");

      // axios
      //   .get("api/performance_evaluate", {
      //     params,
      //     headers: { Authorization: `Bearer ${token}` },
      //   })
      //   .then(function(response) {
      //     console.log(response.data);
      //     _this.record = response.data;
      //     if (_this.record.length > 0) {
      //       _this.views = _this.record[0];

      //       var e_score = 0.0;
      //       var m_score = 0.0;
      //       var e_cnt = 0;
      //       var m_cnt = 0;
      //       for(var i = 0; i < _this.views.agenda.length; i++)
      //       {
      //         if(_this.views.agenda[i].emp_score != -1)
      //         {
      //           e_score += parseInt((_this.views.agenda[i].emp_score) === '' ? "0" : _this.views.agenda[i].emp_score);
      //           e_cnt += 1;
      //         }
              
      //         if(_this.views.agenda[i].mag_score != -1)
      //         {
      //           m_score += parseInt((_this.views.agenda[i].mag_score) === '' ? "0" : _this.views.agenda[i].mag_score);
      //           m_cnt += 1;
      //         }
      //       }

      //       if(e_cnt === 0)
      //         _this.emp_avg = 0;
      //       else
      //         _this.emp_avg = (e_score / e_cnt).toFixed(1);

      //       if(m_cnt === 0)
      //         _this.mag_avg = 0;
      //       else
      //         _this.mag_avg = (m_score / m_cnt).toFixed(1);
            

      //       var e_score1 = 0.0;
      //       var m_score1 = 0.0;
      //       var e_cnt1 = 0;
      //       var m_cnt1 = 0;
      //       for(var i = 0; i < _this.views.agenda1.length; i++)
      //       {
      //         if(_this.views.agenda1[i].emp_score != -1)
      //         {
      //           e_score1 += parseInt((_this.views.agenda1[i].emp_score) === '' ? "0" : _this.views.agenda1[i].emp_score);
      //           e_cnt1 += 1;
      //         }
              
      //         if(_this.views.agenda1[i].mag_score != -1)
      //         {
      //           m_score1 += parseInt((_this.views.agenda1[i].mag_score) === '' ? "0" : _this.views.agenda1[i].mag_score);
      //           m_cnt1 += 1;
      //         }
      //       }

      //       if(e_cnt1 === 0)
      //         _this.emp_avg1 = 0;
      //       else
      //         _this.emp_avg1 = (e_score1 / e_cnt1).toFixed(1);

      //       if(m_cnt1 === 0)
      //         _this.mag_avg1 = 0;
      //       else
      //         _this.mag_avg1 = (m_score1 / m_cnt1).toFixed(1);


      //       var e_score2 = 0.0;
      //       var m_score2 = 0.0;
      //       var e_cnt2 = 0;
      //       var m_cnt2 = 0;
      //       for(var i = 0; i < _this.views.agenda2.length; i++)
      //       {
      //         if(_this.views.agenda2[i].emp_score != -1)
      //         {
      //           e_score2 += parseInt((_this.views.agenda2[i].emp_score) === '' ? "0" : _this.views.agenda2[i].emp_score);
      //           e_cnt2 += 1;
      //         }
              
      //         if(_this.views.agenda2[i].mag_score != -1)
      //         {
      //           m_score2 += parseInt((_this.views.agenda2[i].mag_score) === '' ? "0" : _this.views.agenda2[i].mag_score);
      //           m_cnt2 += 1;
      //         }
      //       }

      //       if(e_cnt2 === 0)
      //         _this.emp_avg2 = 0;
      //       else
      //         _this.emp_avg2 = (e_score2 / e_cnt2).toFixed(1);

      //       if(m_cnt2 === 0)
      //         _this.mag_avg2 = 0;
      //       else
      //         _this.mag_avg2 = (m_score2 / m_cnt2).toFixed(1);

            _window.jQuery(".mask").toggle();
            _window.jQuery("#Modal_4").toggle();
          // }
        // })
        // .catch(function(error) {
        //   console.log(error);
        // });
      
    },

    evalua: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to evaluate",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } else {

      var record = this.shallowCopy(
          this.receive_records.find((element) => element.id == this.proof_id)
        );

      if(record.user_id == this.user_id && record.user_complete_at != "")
        return;

      if(record.create_id == this.user_id && record.status == "Done" && record.comment_done_at != "")
      {
        Swal.fire({
          html: 'You already submitted "Comments after communication".',
          icon: "info",
          confirmButtonText: "OK",
        });
        return;
      }

      if(record.create_id == this.user_id && record.status != "Done" && record.manager_complete_at != "")
      {
        return;
      }

      if(record.create_id == this.user_id && record.manager_complete_at != "" && record.comment_done_at != "")
      {
        Swal.fire({
          html: 'You already submitted "Comments after communication".',
          icon: "info",
          confirmButtonText: "OK",
        });
        return;
      }
        

      if(record.create_id != this.user_id && record.user_id != this.user_id)
        return;

      let _this = this;
      let _window = window;

      const params = {
        id: this.proof_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/leadership_assessment", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.record = response.data;
          if (_this.record.length > 0) {
            _this.evals = _this.record[0];
            _window.jQuery(".mask").toggle();

            if(record.create_id == _this.user_id && record.status == "Done")
            _window.jQuery("#Modal_4").toggle();
            else
            _window.jQuery("#Modal_2").toggle();

            _this.reset_evaluate();
          }
        })
        .catch(function(error) {
          console.log(error);
        });
      }
    },

    edit_detai: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to edit",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } else {
        window.jQuery(".mask").toggle();
        window.jQuery("#Modal_3").toggle();
      }
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

      this.e_title = this.shallowCopy(
        this.position.find((element) => element.did == this.record.did)
      ).items;

      this.e_department = this.record.did;
      this.e_tid = this.record.tid;

      this.e_sn = this.record.agenda.length;
      this.e_sn1 = this.record.agenda1.length;
      this.e_sn2 = this.record.agenda2.length;
    },

    can_delete(manager){
      var can_save = false;

      if(manager === this.user_id)
        can_save = true;

      if(this.department.trim() == '')
      { 
        if(this.title.trim().toUpperCase() == 'OWNER')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'MANAGING DIRECTOR')
          can_save = true;

        if(this.title.trim().toUpperCase() == 'CHIEF ADVISOR')
          can_save = true;
      }

      if(this.title.trim().toUpperCase() == 'VALUE DELIVERY MANAGER')
          can_save = true;
      
      return can_save;
    },

    getLeadershipAssessmentControl: function() {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);

      axios({
          method: 'get',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/leadership_assessment_control',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.leadership_assessment = response.data.leadership_assessment;
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

    is_add_review_privilege() {
          
      return this.leadership_assessment;
    },


    remove() {
    

      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to delete",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      if(this.record.status == 1)
      {
        Swal.fire({
          text: "No one is allowed to delete the ongoing leadership assessment record.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      if(this.record.status == 2)
        {
          Swal.fire({
            text: "No one is allowed to delete the completed leadership assessment record.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

        if(this.leadership_assessment == false)
          {
            Swal.fire({
              text: "You are not allowed to delete this leadership assessment record. ",
              icon: "warning",
              confirmButtonText: "OK",
            });
    
            return;
          }

      let _this = this;

      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete this leadership assessment record?",
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
          form_Data.append("pid", _this.record.id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/leadership_assessment_delete",
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


    getEmployees: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_employees", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.employees = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    reset_evaluate: function() {
      this.comment1 = "";
      this.comment2 = "";
      this.comment3 = "";
      this.comment5 = "";
      this.comment4 = "";
      this.comment6 = "";

      var opt = this.$refs.opt;
      for (i = 0; i < opt.length; i++) {
        opt[i].value = "";
      }

      var opt1 = this.$refs.opt1;
      for (i = 0; i < opt1.length; i++) {
        opt1[i].value = "";
      }

      var grade1 = this.$refs.grade1;
      for (i = 0; i < grade1.length; i++) {
        grade1[i].value = 10;
      }

      var opt2 = this.$refs.opt2;
      for (i = 0; i < opt2.length; i++) {
        opt2[i].value = "";
      }

      var grade2 = this.$refs.grade2;
      for (i = 0; i < grade2.length; i++) {
        grade2[i].value = 10;
      }

      var grade = this.$refs.grade;
      for (i = 0; i < grade.length; i++) {
        grade[i].value = 10;
      }

      this.avg = 10.0;
      this.avg1 = 10.0;
      this.avg2 = 10.0;

    },


    reset: function() {
      this.submit = false;

      this.editing = false;

      this.comment1 = "";
      this.comment2 = "";
      this.comment3 = "";
      this.comment5 = "";
      this.comment4 = "";
      this.comment6 = "";

      this.employee = "";
      this.review_month = "";
      this.review_month_1 = "";
      this.month_type = 0;
      this.review_next_month = "";
      this.template = "";
      this.templates = [];

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


  },
});
