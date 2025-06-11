Vue.component("v-select", VueSelect.VueSelect);
var app = new Vue({
  el: "#app",
  data: {
    receive_records: [],
    leave_records: {},
    holiday_records: {},

    payess1: [],
    payess2: [],
    payess3: [],
    payess4: [],
    payess5: [],
    payess6: [],
    payess7: [],
    payess8: [],

    access1: [],
    access2: [],
    access3: [],
    access4: [],
    access5: [],
    access6: [],
    access7: [],

    knowledge: [],

    vote1: [],
    vote2: [],

    payees: [],

    schedule_confirm:[],
    halfday: [],
    tag_management: [],
    soa: [],
    transmittal: [],
    edit_emp: [],
    edit_basic: [],
    office_items : [],
    office_item_approve: [],
    office_item_release: [],
    limited_access : [],
    inventory_checker : [],
    inventory_approver : [],
    frozen_office: "",
    quotation_control: [],
    cost_lighting: [],
    cost_furniture: [],
    leadership_assessment: [],
    special_agreement: [],
    for_user: [],
    for_profile: [],
    product_edit: [],
    product_duplicate: [],
    product_delete: [],
    inventory_modify: [],
  },

  created() {
    this.getRecords();
    this.getPayees();
  },

  computed: {},

  mounted() {},

  methods: {
    getRecords: function(kind) {
      let _this = this;
      const params = {
        action: 1,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("../api/access_control", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            if (kind === 1 || kind === undefined)
              _this.payess1 = res.data[0]["payess1"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 2 || kind === undefined)
              _this.payess2 = res.data[0]["payess2"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 3 || kind === undefined)
              _this.payess3 = res.data[0]["payess3"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 4 || kind === undefined)
              _this.payess4 = res.data[0]["payess4"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 5 || kind === undefined)
              _this.payess5 = res.data[0]["payess5"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 6 || kind === undefined)
              _this.payess6 = res.data[0]["payess6"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 7 || kind === undefined)
              _this.payess7 = res.data[0]["payess7"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 8 || kind === undefined)
              _this.payess8 = res.data[0]["payess8"].split(",").filter(function (el) {
                return el != "";
              });

            if (kind === 9 || kind === undefined)
              _this.access1 = res.data[0]["access1"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 10 || kind === undefined)
              _this.access2 = res.data[0]["access2"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 11 || kind === undefined)
              _this.access3 = res.data[0]["access3"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 12 || kind === undefined)
              _this.access4 = res.data[0]["access4"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 13 || kind === undefined)
              _this.access5 = res.data[0]["access5"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 14 || kind === undefined)
              _this.access6 = res.data[0]["access6"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 15 || kind === undefined)
              _this.access7 = res.data[0]["access7"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 16 || kind === undefined)
              _this.knowledge = res.data[0]["knowledge"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 17 || kind === undefined)
              _this.vote1 = res.data[0]["vote1"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 18 || kind === undefined)
              _this.vote2 = res.data[0]["vote2"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 19 || kind === undefined)
              _this.schedule_confirm = res.data[0]["schedule_confirm"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 20 || kind === undefined)
              _this.halfday = res.data[0]["halfday"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 21 || kind === undefined)
              _this.tag_management = res.data[0]["tag_management"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 22 || kind === undefined)
              _this.soa = res.data[0]["soa"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 23 || kind === undefined)
              _this.transmittal = res.data[0]["transmittal"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 24 || kind === undefined)
              _this.edit_emp = res.data[0]["edit_emp"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 25 || kind === undefined)
              _this.edit_basic = res.data[0]["edit_basic"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 26 || kind === undefined)
              _this.office_items = res.data[0]["office_items"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 27 || kind === undefined)
              _this.office_item_approve = res.data[0]["office_item_approve"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 28 || kind === undefined)
              _this.office_item_release = res.data[0]["office_item_release"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 29 || kind === undefined)
              _this.limited_access = res.data[0]["limited_access"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 30 || kind === undefined)
              _this.inventory_checker = res.data[0]["inventory_checker"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 31 || kind === undefined)
              _this.inventory_approver = res.data[0]["inventory_approver"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 32 || kind === undefined)
              _this.frozen_office = res.data[0]["frozen_office"];
              if (kind === 33 || kind === undefined)
                _this.quotation_control = res.data[0]["quotation_control"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 34 || kind === undefined)
                _this.cost_lighting = res.data[0]["cost_lighting"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 35 || kind === undefined)
                _this.cost_furniture = res.data[0]["cost_furniture"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 36 || kind === undefined)
                _this.leadership_assessment = res.data[0]["leadership_assessment"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 37 || kind === undefined)
                _this.special_agreement = res.data[0]["special_agreement"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 38 || kind === undefined)
                _this.for_user = res.data[0]["for_user"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 39 || kind === undefined)
                _this.for_profile = res.data[0]["for_profile"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 40 || kind === undefined)
                _this.product_edit = res.data[0]["product_edit"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 41 || kind === undefined)
                _this.product_duplicate = res.data[0]["product_duplicate"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 42 || kind === undefined)
                _this.product_delete = res.data[0]["product_delete"].split(",").filter(function (el) {
                  return el != "";
                });
              if (kind === 43 || kind === undefined)
                _this.inventory_modify = res.data[0]["inventory_modify"].split(",").filter(function (el) {
                  return el != "";
                });

          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getPayees: function() {
      var form_Data = new FormData();
      let _this = this;
      this.action = 5; //select payee
      form_Data.append("action", this.action);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/add_or_edit_price_record",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          for (var i = 0; i < response.data.length; i++) {
            _this.payees.push(response.data[i].username);
          }
          console.log(_this.payees);
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

    save: function(kind) {
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("action", 3);
      form_Data.append("payess1", this.payess1.toString());
      form_Data.append("payess2", this.payess2.toString());
      form_Data.append("payess3", this.payess3.toString());
      form_Data.append("payess4", this.payess4.toString());
      form_Data.append("payess5", this.payess5.toString());
      form_Data.append("payess6", this.payess6.toString());
      form_Data.append("payess7", this.payess7.toString());
      form_Data.append("payess8", this.payess8.toString());
      form_Data.append("access1", this.access1.toString());
      form_Data.append("access2", this.access2.toString());
      form_Data.append("access3", this.access3.toString());
      form_Data.append("access4", this.access4.toString());
      form_Data.append("access5", this.access5.toString());
      form_Data.append("access6", this.access6.toString());
      form_Data.append("access7", this.access7.toString());
      form_Data.append("knowledge", this.knowledge.toString());
      form_Data.append("vote1", this.vote1.toString());
      form_Data.append("vote2", this.vote2.toString());
      form_Data.append("schedule_confirm", this.schedule_confirm.toString());
      form_Data.append("halfday", this.halfday.toString());
      form_Data.append("tag_management", this.tag_management.toString());
      form_Data.append("soa", this.soa.toString());
      form_Data.append("transmittal", this.transmittal.toString());
      form_Data.append("edit_emp", this.edit_emp.toString());
      form_Data.append("edit_basic", this.edit_basic.toString());
      form_Data.append("office_items", this.office_items.toString());
      form_Data.append("office_item_approve", this.office_item_approve.toString());
      form_Data.append("office_item_release", this.office_item_release.toString());
      form_Data.append("limited_access", this.limited_access.toString());
      form_Data.append("inventory_checker", this.inventory_checker.toString());
      form_Data.append("inventory_approver", this.inventory_approver.toString());
      form_Data.append("frozen_office", this.frozen_office);
      form_Data.append("quotation_control", this.quotation_control);
      form_Data.append("cost_lighting", this.cost_lighting);
      form_Data.append("cost_furniture", this.cost_furniture);
      form_Data.append("leadership_assessment", this.leadership_assessment);
      form_Data.append("special_agreement", this.special_agreement);
      form_Data.append("for_user", this.for_user);
      form_Data.append("for_profile", this.for_profile);
      form_Data.append("product_edit", this.product_edit);
      form_Data.append("product_duplicate", this.product_duplicate);
      form_Data.append("product_delete", this.product_delete);
      form_Data.append("inventory_modify", this.inventory_modify);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/access_control",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: "Update Success.",
            icon: "success",
            confirmButtonText: "OK",
          });

          _this.reset();
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

    cancel: function(kind) {
      this.getRecords(kind);
    },

    reset: function() {
      this.getRecords();
    },
  },
});
