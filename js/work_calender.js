    var app = new Vue({
    data:{
        name:'',
        today:'',
		items:[],
		agenda:[],
		messages:[],
        id:0,
        download_type:'docx',
		file_day:'',
		fileArray:[],
		filename:[],
    },
    created () {
      this.getMonthDay();
      this.getUserName();
  },
     methods:{

        addMain2:function(details,main,du, calendar){

            if(!main.Allday) {
                if ((main.Starttime != "") && (main.Endtime != "") && (main.Endtime >= main.Starttime)) {
                    //good to go
                    console.log('go');
                }
                else
                {
                    Swal.fire({
                        text: JSON.stringify("End-time has to behide start-time!"),
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    return;
                }
            }


            this.action = 22;//add
            var token = localStorage.getItem('token');
            var form_Data = new FormData();
            let _this = this;
            form_Data.append('jwt', token);
            form_Data.append('title', main.Title);
            form_Data.append('all_day', main.Allday);
            form_Data.append('start_time', main.Starttime);
            form_Data.append('end_time', main.Endtime);
            form_Data.append('color', main.Color);
            form_Data.append('text_color', 'white');
            form_Data.append('project', main.Project);
            form_Data.append('sales_executive', main.Sales_Executive);
            form_Data.append('project_in_charge', main.Project_in_charge);
            form_Data.append('installer_needed', main.Installer_needed);
            form_Data.append('installer_needed_location', main.Location_Products_to_Bring);
            form_Data.append('things_to_bring', main.Things_to_Bring);
            form_Data.append('things_to_bring_location', main.Location_Things_to_Bring);
            form_Data.append('products_to_bring', main.Products_to_Bring);
            if(du != 2){
                form_Data.append('products_to_bring_files', _this.filename);
            }
            else
            {
                form_Data.append('products_to_bring_files', main.File_name);
            }
            form_Data.append('service', main.Service);
            form_Data.append('driver', main.Driver);
            form_Data.append('back_up_driver', main.Back_up_Driver);
            form_Data.append('photoshoot_request', main.Photoshoot_Request);
            form_Data.append('notes', main.Notes);
            form_Data.append('is_enabled', main.is_enabled);
            form_Data.append('action', this.action);
            form_Data.append('created_by', this.name);
            form_Data.append('today', this.file_day);

            // make it in one process
            form_Data.append('detail_list', JSON.stringify(details));

            for( var i = 0; i < this.fileArray.length; i++ ){
                let file = this.fileArray[i];
                form_Data.append('files[' + i + ']', file);
                }

            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/work_calender_main',
                data: form_Data
            })
                .then(function(response) {

                if(isNaN(response.data))
                {
                    Swal.fire({
                        text: JSON.stringify("Calendar schedule insert fail"),
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    })
                    return;
                }

                //handle success
                if(main.Allday)
                {
                    calendar.addEvent({
                        title: main.Title,
                        start: main.Date,
                        allDay: main.Allday,
                        description: main,
                        borderColor: main.Color,
                        backgroundColor: main.Color,
        
                    });
                }else{
                    if ((main.Starttime != "") && (main.Endtime != "") && (main.Endtime >= main.Starttime)) {

                        calendar.addEvent({
                            title: main.Title,
                            start: main.Date + "T" + main.Starttime,
                            end: main.Date + "T" + main.Endtime,
                            allDay: main.Allday,
                            description: main,
                            borderColor: main.Color,
                            backgroundColor: main.Color,
        
                        });
                    }
                }

                reload();

            })
            .catch(function(error) {
                //handle error
                Swal.fire({
                    text: JSON.stringify(error),
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
            });
        },
         
        addMain:function(details,main,du){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
                  form_Data.append('jwt', token);
                  form_Data.append('title', main.Title);
				  form_Data.append('all_day', main.Allday);
                  form_Data.append('start_time', main.Starttime);
                  form_Data.append('end_time', main.Endtime);
                  form_Data.append('color', main.Color);
                  form_Data.append('text_color', 'white');
                  form_Data.append('project', main.Project);
                  form_Data.append('sales_executive', main.Sales_Executive);
                  form_Data.append('project_in_charge', main.Project_in_charge);
                  form_Data.append('installer_needed', main.Installer_needed);
                  form_Data.append('installer_needed_location', main.Location_Products_to_Bring);
                  form_Data.append('things_to_bring', main.Things_to_Bring);
                  form_Data.append('things_to_bring_location', main.Location_Things_to_Bring);
                  form_Data.append('products_to_bring', main.Products_to_Bring);
				  if(du != 2){
					form_Data.append('products_to_bring_files', _this.filename);
				  }
				  else
				  {
					form_Data.append('products_to_bring_files', main.File_name);
				  }
                  form_Data.append('service', main.Service);
                  form_Data.append('driver', main.Driver);
                  form_Data.append('back_up_driver', main.Back_up_Driver);
                  form_Data.append('photoshoot_request', main.Photoshoot_Request);
                  form_Data.append('notes', main.Notes);
                  form_Data.append('is_enabled', main.is_enabled);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/work_calender_main',
                      data: form_Data
                  })
                      .then(function (response) {
                          console.log(details);
                          console.log(response);
                          console.log(response.data[0]);
                          _this.addDetails(response.data[0],details,main.Date);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                      //this.upload();
                      //this.reload();
          },
        
        addDetails:function(mainId,addDetails,date){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          let _this = this;
          for (var i = 0; i < addDetails.length; i++) {
				  var form_Data = new FormData();
                  form_Data.append('jwt', token);
                  form_Data.append('main_id', mainId);
                  form_Data.append('location', addDetails[i].location);
                  form_Data.append('agenda', addDetails[i].agenda);
				  if(addDetails[i].appointtime !='' && addDetails[i].appointtime != 'Invalid date'){
					var valid = moment(addDetails[i].appointtime, "YYYY-MM-DD HH:mm", true).isValid(); 
					var valids = moment(addDetails[i].appointtime, "YYYY-MM-DD HH:mm:ss", true).isValid();
					if(!valid && !valids){
						form_Data.append('appoint_time', date +' '+ addDetails[i].appointtime);
					}
					else
					{
						form_Data.append('appoint_time', addDetails[i].appointtime);
					}
				  }

				  if(addDetails[i].endtime !='' && addDetails[i].endtime != 'Invalid date'){
					var valide = moment(addDetails[i].endtime, "YYYY-MM-DD HH:mm", true).isValid();
					var valides = moment(addDetails[i].endtime, "YYYY-MM-DD HH:mm:ss", true).isValid(); 
					if(!valide && !valides){
						form_Data.append('end_time', date +' '+ addDetails[i].endtime);
					}
					else
					{
						form_Data.append('end_time', addDetails[i].endtime);
					}
				  }
				  form_Data.append('sort', addDetails[i].sort);
                  form_Data.append('is_enabled', addDetails[i].is_enabled);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/work_calender_detail',
                      data: form_Data
                  })
                      .then(function (response) {
                          //this.addDetails(response.data[0]);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                      //this.upload();
                     // this.reload();
          }
		  _this.upload();
		  reload();
        },
		  
		getMain: function(){
		  var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;
		_this.items = [];
        this.action = 1;//select
        form_Data.append('jwt', token);
        form_Data.append('action', this.action);

        axios({
          method: 'post',
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          url: 'api/work_calender_main',
          data: form_Data
        })
            .then(function(response) {
              //handle success
			//var data = JSON.parse(response.data);
			for(var i = 0;i<response.data.length;i++) {
				var agendas = [];
				var isAll = false;
				var Lasteditor = '';
				var photoshoot = 'No';
				if(response.data[i].all_day == '1'){
					isAll = true;
				}
				if(response.data[i].photoshoot_request == '1'){
					photoshoot = 'Yes';
				}
				if(response.data[i].updated_by != '' && response.data[i].updated_by != null)
				{
					Lasteditor = response.data[i].updated_by + ' at ' + response.data[i].updated_at;
				}
				else
				{
					Lasteditor = response.data[i].created_by + ' at ' + response.data[i].created_at;
				}
				for(var j = 0;j<_this.agenda.length;j++) {
				if(_this.agenda[j].main_id == response.data[i].id){
					agendas.push({
						agenda:UnescapeHTML(_this.agenda[j].agenda),
						appointtime:moment(_this.agenda[j].appoint_time).format('HH:mm'),
						endtime:moment(_this.agenda[j].end_time).format('HH:mm'),
						sort:_this.agenda[j].sort,
						location:UnescapeHTML(_this.agenda[j].location)
					});
				}
				
				}
				//整理檔案
				response.data[i].products_to_bring_files = response.data[i].products_to_bring_files.replaceAll(',','","');
				if(response.data[i].products_to_bring_files.indexOf("\"")==0){
                              response.data[i].products_to_bring_files = "[" + response.data[i].products_to_bring_files + "]";
                              response.data[i].products_to_bring_files = JSON.parse(response.data[i].products_to_bring_files);
                          }else{
                              response.data[i].products_to_bring_files = "[\"" + response.data[i].products_to_bring_files + "\"]";
                              response.data[i].products_to_bring_files = JSON.parse(response.data[i].products_to_bring_files);
                          }
				var files = "";
				response.data[i].products_to_bring_files.forEach((element)=>{
					var file_str = "<a href='https://storage.cloud.google.com/calendarfile/"+ element +"' target='_blank'>"+ element +"</a>&emsp;"
                    files += file_str;
                    
				});
					_this.items.push({
						id: response.data[i].id,
						title: response.data[i].title,
						Date: moment(response.data[i].start_time).format('YYYY-MM-DD'),
						start: moment(response.data[i].start_time).format('YYYY-MM-DDTHH:mm'),// will be parsed
						end: moment(response.data[i].end_time).format('YYYY-MM-DDTHH:mm'),
						color: response.data[i].color,
						allDay:isAll,
						description:{
							Title: UnescapeHTML(response.data[i].title),
							Color: response.data[i].color,
							Date: moment(response.data[i].start_time).format('YYYY-MM-DD'),
							Allday:isAll,
							Starttime:moment(response.data[i].start_time).format('HH:mm'),
							Endtime:moment(response.data[i].end_time).format('HH:mm'),
							Project: UnescapeHTML(response.data[i].project),
							Sales_Executive:UnescapeHTML(response.data[i].sales_executive),
							Project_in_charge:UnescapeHTML(response.data[i].project_in_charge),
							Installer_needed:UnescapeHTML(response.data[i].installer_needed),
							Location_Things_to_Bring:UnescapeHTML(response.data[i].things_to_bring_location),
							Things_to_Bring:UnescapeHTML(response.data[i].things_to_bring),
							Location_Products_to_Bring:UnescapeHTML(response.data[i].installer_needed_location),
							Products_to_Bring:UnescapeHTML(response.data[i].products_to_bring),
							Products_to_bring_files:files,
							File_name:response.data[i].products_to_bring_files,
							Service:response.data[i].service,
							Driver:response.data[i].driver,
							Back_up_Driver:response.data[i].back_up_driver,
							Photoshoot_Request:photoshoot,
                            Notes:UnescapeHTML(response.data[i].notes),
                            Lock:response.data[i].lock,
							Agenda:agendas,
							Lasteditor:Lasteditor,
						}
					});
                }
            })
            .catch(function(response) {
              //handle error
              //alert(JSON.stringify(response));
            });
		},
		
		getDetail: function(){
		  	var token = localStorage.getItem('token');
			var form_Data = new FormData();
			let _this = this;
			this.action = 1;//select all
			form_Data.append('jwt', token);
			form_Data.append('action', this.action);

			axios({
				method: 'post',
				headers: {
				'Content-Type': 'multipart/form-data',
				},
				url: 'api/work_calender_detail',
				data: form_Data
			})
			.then(function(response) {
				//handle success
				_this.agenda = response.data
			})
			.catch(function(response) {
				//handle error
				//alert(JSON.stringify(response));
				console.log(response)
			});
        },
        
        updateLock: function(vlock){
			this.action = 8;//update lock status
			var token = localStorage.getItem('token');
			var form_Data = new FormData();
			let _this = this;
            form_Data.append('jwt', token);
			form_Data.append('id', _this.id);
            form_Data.append('lock', vlock);
            form_Data.append('action', _this.action);
            
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/work_calender_main',
                data: form_Data
            })
            .then(function (response) {
                    console.log(details);
                    console.log(response);
                    console.log(response.data[0]);
				  
                    //handle success
                    
            })
            .catch(function (response) {
                    //handle error
				  console.log(response);
            });
        },
        
        export: function() {
   
            var form_Data = new FormData();
          
            const filename = "attendance";
            let _this = this;

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'get',
                    url: 'schedule_data_word?id=' + app.id,
                    data: form_Data,
                    responseType: 'blob', // important
                })
                .then(function(response) {
                      const url = window.URL.createObjectURL(new Blob([response.data]));
                      const link = document.createElement('a');
                      link.href = url;
                     
                        link.setAttribute('download', 'schedule.' + _this.download_type);
                     
                      document.body.appendChild(link);
                      link.click();

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },
		
		updateMain: function(details,main){
			this.action = 3;//update
			var token = localStorage.getItem('token');
			var form_Data = new FormData();
			let _this = this;
            form_Data.append('jwt', token);
			form_Data.append('id', _this.id);
            form_Data.append('title', main.Title);
			form_Data.append('all_day', main.Allday);
            form_Data.append('start_time', main.Starttime);
            form_Data.append('end_time', main.Endtime);
            form_Data.append('color', main.Color);
            form_Data.append('text_color', 'white');
            form_Data.append('project', main.Project);
            form_Data.append('sales_executive', main.Sales_Executive);
            form_Data.append('project_in_charge', main.Project_in_charge);
            form_Data.append('installer_needed', main.Installer_needed);
            form_Data.append('installer_needed_location', main.Location_Products_to_Bring);
            form_Data.append('things_to_bring', main.Things_to_Bring);
            form_Data.append('things_to_bring_location', main.Location_Things_to_Bring);
            form_Data.append('products_to_bring', main.Products_to_Bring);
			if(_this.filename != '')
			{
				form_Data.append('products_to_bring_files', _this.filename);
			}
			else
			{
				form_Data.append('products_to_bring_files', main.File_name);
			}
            form_Data.append('service', main.Service);
            form_Data.append('driver', main.Driver);
            form_Data.append('back_up_driver', main.Back_up_Driver);
            form_Data.append('photoshoot_request', main.Photoshoot_Request);
            form_Data.append('notes', main.Notes);
            form_Data.append('is_enabled', main.is_enabled);
            form_Data.append('action', this.action);
            form_Data.append('updated_by', _this.name);
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/work_calender_main',
                data: form_Data
            })
            .then(function (response) {
                    console.log(details);
                    console.log(response);
                    console.log(response.data[0]);

                    _this.updateDetail(_this.id,details,main.Date);
				    //_this.deleteDetail(_this.id);
                    //_this.addDetails(_this.id,details,main.Date);
                    //handle success
                    
            })
            .catch(function (response) {
                    //handle error
				  console.log(response);
            });
        },
        
        updateDetail: function(mainId,addDetails,date){
			this.action = 7;//delete
			var token = localStorage.getItem('token');
			var form_Data = new FormData();
			let _this = this;
	
			form_Data.append('jwt', token);
			form_Data.append('main_id', mainId);
			form_Data.append('action', _this.action);
			form_Data.append('deleted_by',_this.name);
			axios({
				method: 'post',
				headers: {
					'Content-Type': 'multipart/form-data',
				},
				url: 'api/work_calender_detail',
				data: form_Data
			})
				.then(function (response) {
                    //handle success
                    _this.addDetails(mainId,addDetails,date);
				})
				.catch(function (response) {
                //handle error
                });
        },

		// updateDetail: function(){},
		deleteMain: function(id){
		  this.action = 7;//delete
		  var token = localStorage.getItem('token');
		    var form_Data = new FormData();
		  let _this = this;

		  form_Data.append('jwt', token);
		  form_Data.append('id', id);
		  form_Data.append('action', _this.action);
		  form_Data.append('deleted_by',_this.name);
		  axios({
		 	 method: 'post',
		 	 headers: {
		 		 'Content-Type': 'multipart/form-data',
		 	 },
		 	 url: 'api/work_calender_main',
		 	 data: form_Data
		  })
		 	 .then(function (response) {
		 		 //handle success
		 		 //_this.items = response.data
		 	 })
		 	 .catch(function (response) {
		 		 //handle error
		 	 });
		},
		deleteDetail: function(mainId){
			this.action = 7;//delete
			var token = localStorage.getItem('token');
			var form_Data = new FormData();
			let _this = this;
	
			form_Data.append('jwt', token);
			form_Data.append('main_id', mainId);
			form_Data.append('action', _this.action);
			form_Data.append('deleted_by',_this.name);
			axios({
				method: 'post',
				headers: {
					'Content-Type': 'multipart/form-data',
				},
				url: 'api/work_calender_detail',
				data: form_Data
			})
				.then(function (response) {
					//handle success
				})
				.catch(function (response) {
					//handle error
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
					_this.is_manager = response.data.is_manager;
					_this.manager_leave = response.data.manager_leave;
					_this.al_credit = response.data.annual_leave;
					_this.sl_credit = response.data.sick_leave;
					_this.is_viewer = response.data.is_viewer;
		
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
		getMonthDay:function(){
			let _this = this;
			var today = new Date();
			var first = new Date();
			var dd = ("0" + (today.getDate())).slice(-2);
			var mm = ("0" + (today.getMonth() + 1)).slice(-2);
			var yyyy = today.getFullYear();
			today = yyyy + '-' + mm + '-' + dd;
			first = yyyy + '-' + mm + '-01';
			_this.file_day = yyyy + mm + dd;
			_this.start_date = first;
			_this.end_date = today;
		},
		upload:function(){
			var myArr = this.fileArray;
			var vm = this;
			console.log(myArr);
			myArr.forEach((element, index) => {
			var config = {
				headers: { "Content-Type": "multipart/form-data" }
			}
			var data = myArr[index];
			var myForm = new FormData();
			myForm.append("file", data);
			myForm.append('batch_type', 'proof');
			myForm.append('batch_id', 0);
			myForm.append('today', vm.file_day);
	
	
			axios
				.post("api/work_calender_gcp", myForm, config)
				.then(function(res) {
				if (res.data.code == 0) {
	
					myArr[index].progress = 1;
					vm.$set(vm.fileArray, index, myArr[index]);
					console.log(vm.fileArray, index);
				} else {
					alert(JSON.stringify(res.data));
				}
				})
				.catch(function(err) {
				console.log(err);
				});
			});
        },
	},
    
});
	
	
	var eventObj;
    var icon_function_enable = true;

	function initial() {

        var calendarEl = document.getElementById('calendar');
			
        calendar = new FullCalendar.Calendar(calendarEl, {

            titleFormat: { // will produce something like "Tuesday, September 18, 2018"
                month: '2-digit',
                year: 'numeric',
                day: '2-digit'
            },

            headerToolbar: {
                left: 'prev,next addEventButton',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
			events : app.items,
            //Add Schedule被點擊的方法
            customButtons: {
                addEventButton: {
                    text: 'Add Schedule',
                    click: function () {

                        document.getElementById("myLargeModalLabel").innerText = "Add Schedule";
                        document.getElementById("last_editor").style.display = "none";
                        document.getElementById("btn_reset").style.display = "inline";
                        document.getElementById("btn_add").style.display = "inline";
                        document.getElementById("btn_duplicate").style.display = "none";
                        document.getElementById("btn_export").style.display = "none";
                        document.getElementById("btn_edit").style.display = "none";
                        document.getElementById("btn_delete").style.display = "none";
                        document.getElementById("btn_cancel").style.display = "none";
                        document.getElementById("btn_save").style.display = "none";

                        document.getElementById("btn_lock").style.display = "none";
                        document.getElementById("btn_unlock").style.display = "none";
						
						document.getElementById("sc_product_files").innerHTML = "";
						document.getElementById("upload_input").style = "display: flex; align-items: center; margin-top:1%;";

                        resetSchedule();
                        Change_Schedule_State(false, true);
                        icon_function_enable = true;

                        $('#exampleModalScrollable').modal('toggle');
                    }
                }
            },


            //Schedule被點擊的方法
            eventClick: function (info) {


                document.getElementById("myLargeModalLabel").innerText = "Schedule Details";
                eventObj = info.event;
                resetSchedule();
				app.id = eventObj.id;
                var sc_content = eventObj.extendedProps.description;



                document.getElementById("sc_title").value = sc_content.Title;
                document.getElementById("sc_color").value = sc_content.Color;

                //設定最後編輯者資訊
                document.getElementById("sc_editor").value = sc_content.Lasteditor;
                document.getElementById("last_editor").style.display = "inline";

                document.getElementById("sc_date").value = sc_content.Date;
                document.getElementById("sc_time").checked = sc_content.Allday;
                document.getElementById("sc_stime").value = sc_content.Starttime;
                document.getElementById("sc_etime").value = sc_content.Endtime;
                document.getElementById("sc_project").value = sc_content.Project;
                document.getElementById("sc_sales").value = sc_content.Sales_Executive;
                document.getElementById("sc_incharge").value = sc_content.Project_in_charge;

                var installer = sc_content.Installer_needed.split(",");

                for (i = 0; i < 5; i++) {
                    document.getElementsByName("sc_Installer_needed")[i].checked = false;}

                for( i=0; i<installer.length;i++){

                    if(installer[i] == "AS")
                        document.getElementsByName("sc_Installer_needed")[0].checked = true;

                    if(installer[i] == "RM")
                        document.getElementsByName("sc_Installer_needed")[1].checked = true;

                    if(installer[i] == "RS")
                        document.getElementsByName("sc_Installer_needed")[2].checked = true;

                    if(installer[i] == "CJ")
                        document.getElementsByName("sc_Installer_needed")[3].checked = true;

                    if(installer[i] == "JO")
                        document.getElementsByName("sc_Installer_needed")[4].checked = true;

                    if(installer[i] == "EO")
                        document.getElementsByName("sc_Installer_needed")[5].checked = true;

                    if(installer[i] == "JM")
                        document.getElementsByName("sc_Installer_needed")[6].checked = true;

                }

                //加入Agenda內容(先刪除未儲存的)
                var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

                for (i = agenda_object.length - 1; i > 1; i--) {

                    agenda_object[i].remove();
                }

                for (i = 0; i < sc_content.Agenda.length; i++) {
                    addAgendaitem(sc_content.Agenda[i].location, sc_content.Agenda[i].agenda, sc_content.Agenda[i].appointtime, sc_content.Agenda[i].endtime);
                }

                document.getElementById("sc_location1").value = sc_content.Location_Things_to_Bring;
                document.getElementById("sc_things").value = sc_content.Things_to_Bring;
                document.getElementById("sc_location2").value = sc_content.Location_Products_to_Bring;
                document.getElementById("sc_products").value = sc_content.Products_to_Bring;
				document.getElementById("upload_input").style = "display:none;";
                document.getElementById("sc_product_files").innerHTML = sc_content.Products_to_bring_files;
                if(sc_content.Products_to_bring_files != "<a href='https://storage.cloud.google.com/calendarfile/' target='_blank'></a>&emsp;")
                    app.download_type = 'zip';
                else
                    app.download_type = 'docx';

                document.getElementById("sc_product_files_hide").value = sc_content.File_name;
				document.getElementById("sc_service").value = sc_content.Service;
                document.getElementById("sc_driver1").value = sc_content.Driver;
                document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;


                if( sc_content.Photoshoot_Request == "Yes"){
                    document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
                }
                if( sc_content.Photoshoot_Request == "No"){
                    document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
                }

                document.getElementById("sc_notes").value = sc_content.Notes;
          

                Change_Schedule_State(true, sc_content.Allday);
                icon_function_enable = false;

                document.getElementById("btn_reset").style.display = "none";
                document.getElementById("btn_add").style.display = "none";
                document.getElementById("btn_duplicate").style.display = "inline";
                document.getElementById("btn_export").style.display = "inline";
                document.getElementById("btn_edit").style.display = "inline";
                document.getElementById("btn_delete").style.display = "inline";
                document.getElementById("btn_cancel").style.display = "none";
                document.getElementById("btn_save").style.display = "none";

                // add schedual lock
                document.getElementById("lock").value = sc_content.Lock;
                if(sc_content.Lock != "")
                {
                    document.getElementById("btn_lock").style.display="none";
                    document.getElementById("btn_unlock").style.display="inline";

                    document.getElementById("btn_edit").style.display = "none";
                    document.getElementById("btn_delete").style.display = "none";
                }
                else
                {
                    document.getElementById("btn_lock").style.display="inline";
                    document.getElementById("btn_unlock").style.display="none";
                }

                $('#exampleModalScrollable').modal('toggle');


            },


            //eventDrop: function (info) {
            //    eventObj = info.event;
			//
            //    alert(info.event.title + " has changed Date.\n\nDatabase should be updated too.");
			//
			//
            //},

            editable: false,

        });
        calendar.render();
        clearTimeOut();
        
        if(app.name === "guest")
        {
            document.getElementsByClassName("fc-addEventButton-button")[0].style.visibility="hidden";

            document.getElementById("add_message").style.visibility="hidden";

            document.getElementById("btn_duplicate").style.visibility="hidden";
            // document.getElementById("btn_export").style.visibility="hidden";
            document.getElementById("btn_delete").style.visibility="hidden";
            document.getElementById("btn_edit").style.visibility="hidden";
        }

        if(app.name != "Dennis Lin" && app.name != "Thalassa Wren Benzon" && app.name != "dereck")
        {
            document.getElementById("btn_lock").style.visibility="hidden";
            document.getElementById("btn_unlock").style.visibility="hidden";
        }
    };

	function onChangeFileUpload(e) {
		
		app.filename = [];
		app.fileArray = [];
		for(i=0;i<e.target.files.length;i++){
			const image = e.target.files[i];
			app.filename.push(app.file_day +"_"+ e.target.files[i].name);
			app.fileArray.push(image);
		}
		
	};

    $("button[id='btn_add']").click(function () {

		
        var selected = [];
        $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
            selected.push($(this).val());
        });

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");
        var agenda_content = [];
		var appointtime = '';
		var endtime = '';
        for(i=2; i<agenda_object.length;i++){
			appointtime = '';
			endtime = '';
			if(agenda_object[i].getElementsByTagName("input")[2].value != ''){
				appointtime = document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[2].value;
			}
			if(agenda_object[i].getElementsByTagName("input")[3].value != ''){
				endtime = document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[3].value;
			}
            agenda_content.push(
                {
                    location: agenda_object[i].getElementsByTagName("input")[0].value,
                    agenda: agenda_object[i].getElementsByTagName("input")[1].value,
                    appointtime: appointtime,
                    endtime: endtime,
					sort:i
                }
            );
        }


        var sc_content = {
            Date: document.getElementById("sc_date").value,
            Title: document.getElementById("sc_project").value,
            Color: document.getElementById("sc_color").value,
            Allday: document.getElementById("sc_time").checked,
            
            Starttime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_stime").value,
            Endtime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_etime").value,
            Project: document.getElementById("sc_project").value,
            Sales_Executive: document.getElementById("sc_sales").value,
            Project_in_charge: document.getElementById("sc_incharge").value,
            Agenda: agenda_content,
            Installer_needed: selected.join(),
            Location_Things_to_Bring: document.getElementById("sc_location1").value,
            Things_to_Bring: document.getElementById("sc_things").value,
            Location_Products_to_Bring: document.getElementById("sc_location2").value,
            Products_to_Bring: document.getElementById("sc_products").value,
            Service: document.getElementById("sc_service").value,
            Driver: document.getElementById("sc_driver1").value,
            Back_up_Driver: document.getElementById("sc_driver2").value,
            Photoshoot_Request: $('input[name=sc_Photoshoot_request]:checked').val(),
            Notes:  document.getElementById("sc_notes").value,
            Lock: '',
            is_enable :true
        };



        if(sc_content.Allday) {

            
            sc_content.Starttime = document.getElementById("sc_date").value +' 00:00:00';
            sc_content.Endtime = document.getElementById("sc_date").value +' 23:59:59';
            
        } 

        app.addMain2(agenda_content,sc_content,1, calendar);

        $('#exampleModalScrollable').modal('toggle');
		
        resetSchedule();


    });


    $("button[id='btn_close']").click(function () {
        resetSchedule();
    });

    $("button[id='btn_reset']").click(function () {
        resetSchedule();
    });

    function resetSchedule() {

        document.getElementById("sc_date").value = "";
        document.getElementById("sc_time").checked = true;
        document.getElementById("sc_stime").value = "";
        document.getElementById("sc_stime").disabled = true;
        document.getElementById("sc_etime").value = "";
        document.getElementById("sc_etime").disabled = true;
        document.getElementById("sc_title").value = "";
        document.getElementById("sc_color").value = "";
        document.getElementById("sc_project").value = "";
        document.getElementById("sc_sales").value = "";
        document.getElementById("sc_incharge").value = "";

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";


        $("input[name='sc_Installer_needed']").each(function (i, v) {
            $(v).prop('checked', false);
        });

        document.getElementById("sc_location1").value = "";
        document.getElementById("sc_things").value = "";
        document.getElementById("sc_location2").value = "";
        document.getElementById("sc_products").value = "";
        document.getElementById("sc_service").value = "0";
        document.getElementById("sc_driver1").value = "0";
        document.getElementById("sc_driver2").value = "0";

        $("input[name='sc_Photoshoot_request']").each(function (i, v) {
            $(v).prop('checked', false);
        });

        document.getElementById("sc_notes").value = "";

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

        for(i=agenda_object.length-1; i>1;i--){

            agenda_object[i].remove();
        }

    }


    function Change_Schedule_State(status, time_status) {

        document.getElementById("sc_title").disabled = status;
        document.getElementById("sc_color").disabled = status;
        document.getElementById("sc_date").disabled = status;
        document.getElementById("sc_project").disabled = status;
        document.getElementById("sc_sales").disabled = status;
        document.getElementById("sc_incharge").disabled = status;

        if (status == false) {

            if (time_status == true) {
                document.getElementById("sc_time").disabled = false;
                document.getElementById("sc_stime").disabled = true;
                document.getElementById("sc_etime").disabled = true;
            }
            else{
                document.getElementById("sc_time").disabled = false;
                document.getElementById("sc_stime").disabled = false;
                document.getElementById("sc_etime").disabled = false;
            }

        } else {
            document.getElementById("sc_time").disabled = status;
            document.getElementById("sc_stime").disabled = status;
            document.getElementById("sc_etime").disabled = status;
        }

        document.getElementById("sc_tb_location").disabled = status;
        document.getElementById("sc_tb_agenda").disabled = status;
        document.getElementById("sc_tb_appointtime").disabled = status;
        document.getElementById("sc_tb_endtime").disabled = status;

        document.getElementById("add_agenda").disabled = status;




        document.getElementsByName("sc_Installer_needed")[0].disabled = status;
        document.getElementsByName("sc_Installer_needed")[1].disabled = status;
        document.getElementsByName("sc_Installer_needed")[2].disabled = status;
        document.getElementsByName("sc_Installer_needed")[3].disabled = status;
        document.getElementsByName("sc_Installer_needed")[4].disabled = status;
        document.getElementsByName("sc_Installer_needed")[5].disabled = status;
        document.getElementsByName("sc_Installer_needed")[6].disabled = status;

        document.getElementById("sc_location1").disabled = status;
        document.getElementById("sc_things").disabled = status;
        document.getElementById("sc_location2").disabled = status;
        document.getElementById("sc_products").disabled = status;
        document.getElementById("sc_service").disabled = status;
        document.getElementById("sc_driver1").disabled = status;
        document.getElementById("sc_driver2").disabled = status;

        document.getElementsByName("sc_Photoshoot_request")[0].disabled = status;
        document.getElementsByName("sc_Photoshoot_request")[1].disabled = status;

        document.getElementById("sc_notes").disabled = status;

    }


    $(document).on('click', '#btn_edit', function () {

        //調整最下方出現按鈕
        document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_export").style.display = "inline";
        document.getElementById("btn_edit").style.display = "none";
        document.getElementById("btn_delete").style.display = "none";
        document.getElementById("btn_cancel").style.display = "inline";
        document.getElementById("btn_save").style.display = "inline";

        document.getElementById("btn_lock").style.display = "none";
        document.getElementById("btn_unlock").style.display = "none";

        document.getElementById("last_editor").style.display = "none";

		document.getElementById("upload_input").style = "display: flex; align-items: center; margin-top:1%;";
        //切換元件成為可修改狀態
        Change_Schedule_State(false, eventObj.extendedProps.description.Allday);
        icon_function_enable = true;

    });


    $(document).on('click', '#btn_delete', function () {

        app.deleteMain(eventObj.id);
		eventObj.remove();
        $('#exampleModalScrollable').modal('toggle');

    });


    $(document).on('click', '#btn_duplicate', function () {

        var sc_content = eventObj.extendedProps.description;
        sc_content.Lock = "";

		console.log(sc_content);
        if(sc_content.Allday) {

            
			sc_content.Starttime = sc_content.Date +' 00:00:00';
            sc_content.Endtime = sc_content.Date +' 23:59:59';
        } else {

            if ((sc_content.Starttime != "") && (sc_content.Endtime != "") && (sc_content.Endtime >= sc_content.Starttime)) {

            
				sc_content.Starttime = sc_content.Date +' '+ sc_content.Starttime;
				sc_content.Endtime = sc_content.Date +' '+ sc_content.Endtime;
            }
        }
		app.addMain2(sc_content.Agenda,sc_content,2, calendar);

        $('#exampleModalScrollable').modal('toggle');

        resetSchedule();

    });

    $(document).on('click', '#btn_export', function () {

        
		app.export();


    });

    $(document).on('click', '#btn_cancel', function () {

        var sc_content = eventObj.extendedProps.description;

        document.getElementById("sc_title").value = sc_content.Title;
        document.getElementById("sc_color").value = sc_content.Color;

        //設定最後編輯者資訊
        document.getElementById("sc_editor").value = sc_content.Lasteditor;
        document.getElementById("last_editor").style.display = "inline";

        document.getElementById("sc_date").value = sc_content.Date;
        document.getElementById("sc_time").checked = sc_content.Allday;
        document.getElementById("sc_stime").value = sc_content.Starttime;
        document.getElementById("sc_etime").value = sc_content.Endtime;
        document.getElementById("sc_project").value = sc_content.Project;
        document.getElementById("sc_sales").value = sc_content.Sales_Executive;
        document.getElementById("sc_incharge").value = sc_content.Project_in_charge;

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";

        var installer = sc_content.Installer_needed.split(",");

        for(i=0; i<5; i++){
            document.getElementsByName("sc_Installer_needed")[i].checked = false;}

        for (i = 0; i < installer.length; i++) {

            if (installer[i] == "AS")
                document.getElementsByName("sc_Installer_needed")[0].checked = true;

            if (installer[i] == "RM")
                document.getElementsByName("sc_Installer_needed")[1].checked = true;

            if (installer[i] == "RS")
                document.getElementsByName("sc_Installer_needed")[2].checked = true;

            if (installer[i] == "CJ")
                document.getElementsByName("sc_Installer_needed")[3].checked = true;

            if (installer[i] == "JO")
                document.getElementsByName("sc_Installer_needed")[4].checked = true;

            if (installer[i] == "EO")
                document.getElementsByName("sc_Installer_needed")[5].checked = true;

            if (installer[i] == "JM")
                document.getElementsByName("sc_Installer_needed")[6].checked = true;

        }

        //加入Agenda內容(先刪除未儲存的)
        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");

        for (i = agenda_object.length - 1; i > 1; i--) {

            agenda_object[i].remove();
        }

        for (i = 0; i < sc_content.Agenda.length; i++) {
            addAgendaitem(sc_content.Agenda[i].location, sc_content.Agenda[i].agenda, sc_content.Agenda[i].appointtime, sc_content.Agenda[i].endtime);
        }

        document.getElementById("sc_location1").value = sc_content.Location_Things_to_Bring;
        document.getElementById("sc_things").value = sc_content.Things_to_Bring;
        document.getElementById("sc_location2").value = sc_content.Location_Products_to_Bring;
        document.getElementById("sc_products").value = sc_content.Products_to_Bring;
        document.getElementById("sc_service").value = sc_content.Service;
        document.getElementById("sc_driver1").value = sc_content.Driver;
        document.getElementById("sc_driver2").value = sc_content.Back_up_Driver;


        if (sc_content.Photoshoot_Request == "Yes") {
            document.getElementsByName("sc_Photoshoot_request")[0].checked = true;
        }
        if (sc_content.Photoshoot_Request == "No") {
            document.getElementsByName("sc_Photoshoot_request")[1].checked = true;
        }

        document.getElementById("sc_notes").value = sc_content.Notes;

        Change_Schedule_State(true, sc_content.Allday);
        icon_function_enable = false;

        document.getElementById("upload_input").style.display = "none";
		document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_export").style.display = "inline";
        document.getElementById("btn_edit").style.display = "inline";
        document.getElementById("btn_delete").style.display = "inline";
        document.getElementById("btn_cancel").style.display = "none";
        document.getElementById("btn_save").style.display = "none";

        if(document.getElementById("lock").value == "Y")
        {
            document.getElementById("btn_lock").style.display = "none";
            document.getElementById("btn_unlock").style.display = "inline";
        }
        else
        {
            document.getElementById("btn_lock").style.display = "inline";
            document.getElementById("btn_unlock").style.display = "none";
        }
    });

    $(document).on('click', '#btn_lock', async function () {
        document.getElementById("lock").value = "Y";
        await app.updateLock("Y");

        var sc_content = eventObj.extendedProps.description;
        sc_content.Lock = "Y";

        document.getElementById("btn_lock").style.display = "none";
        document.getElementById("btn_unlock").style.display = "inline";

        document.getElementById("btn_edit").style.display = "none";
        document.getElementById("btn_delete").style.display = "none";
    });

    $(document).on('click', '#btn_unlock', async function () {
        document.getElementById("lock").value = "";
        await app.updateLock("");

        var sc_content = eventObj.extendedProps.description;
        sc_content.Lock = "";

        document.getElementById("btn_lock").style.display = "inline";
        document.getElementById("btn_unlock").style.display = "none";

        document.getElementById("btn_edit").style.display = "inline";
        document.getElementById("btn_delete").style.display = "inline";
    });

    $(document).on('click', '#btn_save', function () {


        var selected = [];
        $("[name=sc_Installer_needed]:checkbox:checked").each(function () {
            selected.push($(this).val());
        });
		
		var files = "";
		if(app.filename != '')
		{
			app.filename.forEach((element)=>{
				var file_str = "<a href='https://storage.cloud.google.com/calendarfile/"+ element +"' target='_blank'>"+ element +"</a>&emsp;"
				files += file_str;
			});
		}
		else if(document.getElementById("sc_product_files_hide").value != '')
		{
			var splits_array = document.getElementById("sc_product_files_hide").value.split(",");
			splits_array.forEach((element)=>{
				var file_str = "<a href='https://storage.cloud.google.com/calendarfile/"+ element +"' target='_blank'>"+ element +"</a>&emsp;"
				files += file_str;
			});
		}
		 
		var time = 	new Date();
		time = time.getFullYear() +'-'+ time.getMonth() + '-' + time.getDate() + ' ' + time.getHours() + ':' + time.getMinutes() + ':' + time.getSeconds(); 
		
		document.getElementById("sc_product_files").innerHTML = files;
		document.getElementById("sc_editor").value = app.name + ' at ' + time;
		document.getElementById("last_editor").style.display = "block";

        var agenda_object = document.getElementById("agenda_table").getElementsByTagName("tr");
        var agenda_content = [];
		var appointtime = '';
		var endtime = '';
        for(i=2; i<agenda_object.length;i++){
			appointtime = '';
			endtime = '';
			if(agenda_object[i].getElementsByTagName("input")[2].value != ''&& agenda_object[i].getElementsByTagName("input")[2].value != null){
				appointtime = document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[2].value;
			}
			if(agenda_object[i].getElementsByTagName("input")[3].value != '' && agenda_object[i].getElementsByTagName("input")[3].value != null){
				endtime = document.getElementById("sc_date").value +' ' +agenda_object[i].getElementsByTagName("input")[3].value;
			}
            agenda_content.push(
                {
                    location: agenda_object[i].getElementsByTagName("input")[0].value,
                    agenda: agenda_object[i].getElementsByTagName("input")[1].value,
                    appointtime: appointtime,
                    endtime: endtime,
					sort:i
                }
            );
        }


        var sc_content = {
            Date: document.getElementById("sc_date").value,
            Title: document.getElementById("sc_project").value,
            Color: document.getElementById("sc_color").value,
            Allday: document.getElementById("sc_time").checked,
            Starttime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_stime").value,
            Endtime: document.getElementById("sc_date").value +' '+ document.getElementById("sc_etime").value,
            Project: document.getElementById("sc_project").value,
            Sales_Executive: document.getElementById("sc_sales").value,
            Project_in_charge: document.getElementById("sc_incharge").value,
            Agenda: agenda_content,
            Installer_needed: selected.join(),
            Location_Things_to_Bring: document.getElementById("sc_location1").value,
            Things_to_Bring: document.getElementById("sc_things").value,
            Location_Products_to_Bring: document.getElementById("sc_location2").value,
            Products_to_Bring: document.getElementById("sc_products").value,
			File_name: document.getElementById("sc_product_files_hide").value,
            Service: document.getElementById("sc_service").value,
            Driver: document.getElementById("sc_driver1").value,
            Back_up_Driver: document.getElementById("sc_driver2").value,
            Photoshoot_Request: $('input[name=sc_Photoshoot_request]:checked').val(),
            Notes: document.getElementById("sc_notes").value,
            Lock: document.getElementById("lock").value,
        };


        if (sc_content.Allday) {

            eventObj.setStart(sc_content.Date + "T00:00");
            eventObj.setEnd(sc_content.Date + "T00:00");
            eventObj.setAllDay(sc_content.Allday);
            eventObj.setProp("title", sc_content.Title);
            eventObj.setProp("borderColor", sc_content.Color);
            eventObj.setProp("backgroundColor", sc_content.Color);
            eventObj.setExtendedProp("description", sc_content);
			
			sc_content.Starttime = document.getElementById("sc_date").value +' 00:00:00';
            sc_content.Endtime = document.getElementById("sc_date").value +' 23:59:59';

        } else {

            if ((sc_content.Starttime != "") && (sc_content.Endtime != "") && (sc_content.Endtime >= sc_content.Starttime)) {

                eventObj.setAllDay(sc_content.Allday);
                eventObj.setStart(sc_content.Date + "T" + sc_content.Starttime);
                eventObj.setEnd(sc_content.Date + "T" + sc_content.Endtime);
                eventObj.setProp("title", sc_content.Title);
                eventObj.setProp("borderColor", sc_content.Color);
                eventObj.setProp("backgroundColor", sc_content.Color);
                eventObj.setExtendedProp("description", sc_content);
            } else {

                sc_content.Allday = eventObj.extendedProps.description.Allday;
                sc_content.Starttime = eventObj.extendedProps.description.Starttime;
                sc_content.Endtime = eventObj.extendedProps.description.Endtime;

                if (eventObj.extendedProps.description.Allday) {

                    eventObj.setStart(sc_content.Date + "T00:00");
                    eventObj.setEnd(sc_content.Date + "T00:00");
                    eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                    eventObj.setProp("title", sc_content.Title);
                    eventObj.setProp("borderColor", sc_content.Color);
                    eventObj.setProp("backgroundColor", sc_content.Color);
                    eventObj.setExtendedProp("description", sc_content);

                } else {
                    eventObj.setAllDay(eventObj.extendedProps.description.Allday);
                    eventObj.setStart(sc_content.Date + "T" + eventObj.extendedProps.description.Starttime);
                    eventObj.setEnd(sc_content.Date + "T" + eventObj.extendedProps.description.Endtime);
                    eventObj.setProp("title", sc_content.Title);
                    eventObj.setProp("borderColor", sc_content.Color);
                    eventObj.setProp("backgroundColor", sc_content.Color);
                    eventObj.setExtendedProp("description", sc_content);
                }

                document.getElementById("sc_time").checked = eventObj.extendedProps.description.Allday;
                document.getElementById("sc_stime").value = eventObj.extendedProps.description.Starttime;
                document.getElementById("sc_etime").value = eventObj.extendedProps.description.Endtime;

            }

        }

        document.getElementById("sc_tb_location").value = "";
        document.getElementById("sc_tb_agenda").value = "";
        document.getElementById("sc_tb_appointtime").value = "";
        document.getElementById("sc_tb_endtime").value = "";

        Change_Schedule_State(true, eventObj.extendedProps.description.Allday);
        icon_function_enable = false;

        document.getElementById("upload_input").style.display = "none";
		document.getElementById("btn_reset").style.display = "none";
        document.getElementById("btn_add").style.display = "none";
        document.getElementById("btn_duplicate").style.display = "inline";
        document.getElementById("btn_export").style.display = "inline";
        document.getElementById("btn_edit").style.display = "inline";
        document.getElementById("btn_delete").style.display = "inline";
        document.getElementById("btn_cancel").style.display = "none";
        document.getElementById("btn_save").style.display = "none";

        if(document.getElementById("lock").value == "Y")
        {
            document.getElementById("btn_lock").style.display = "none";
            document.getElementById("btn_unlock").style.display = "inline";
        }
        else
        {
            document.getElementById("btn_lock").style.display = "inline";
            document.getElementById("btn_unlock").style.display = "none";
        }

		app.updateMain(agenda_content,sc_content);
    });


    $("input[id='sc_time']").change(function () {

        if (this.checked) {
            document.getElementById("sc_stime").disabled = true;
            document.getElementById("sc_etime").disabled = true;
            document.getElementById("sc_stime").value = "";
            document.getElementById("sc_etime").value = "";
        } else {
            document.getElementById("sc_stime").disabled = false;
            document.getElementById("sc_etime").disabled = false;

        }
    });


    var addevent = document.getElementById('add_message');
    var addinput = document.getElementsByClassName('add__input')[0];
    var messageboard = document.getElementsByClassName('messageboard')[0];
    var controlModify = true;

    function autogrow(textarea) {

        var adjustedHeight = textarea.clientHeight;

        adjustedHeight = Math.max(textarea.scrollHeight, adjustedHeight);

        if (adjustedHeight > textarea.clientHeight) {
            textarea.style.height = (adjustedHeight + 2) + 'px';
        }

    }


    var addagenda = document.getElementById('add_agenda');

    addagenda.onclick = function () {

        if(icon_function_enable) {
            addAgendaitem(document.getElementById('sc_tb_location').value.trim(), document.getElementById('sc_tb_agenda').value.trim(), document.getElementById('sc_tb_appointtime').value, document.getElementById('sc_tb_endtime').value);
            document.getElementById('sc_tb_location').value = "";
            document.getElementById('sc_tb_agenda').value = "";
            document.getElementById('sc_tb_appointtime').value = "";
            document.getElementById('sc_tb_endtime').value = "";
        }
    };



    var controlEdit = true;

    function addAgendaitem(location, agenda, appointtime, endtime) {

        var agendaItem = document.createElement("tr");
        var td_1 = document.createElement("td");
        var td_2 = document.createElement("td");
        var td_3 = document.createElement("td");
        var td_4 = document.createElement("td");
        var td_5 = document.createElement("td");
        var input_1 = document.createElement("input");
        var input_2 = document.createElement("input");
        var input_3 = document.createElement("input");
        var input_4 = document.createElement("input");
        var Icon_1 = document.createElement("i");
        var Icon_2 = document.createElement("i");
        var Icon_3 = document.createElement("i");
        var Icon_4 = document.createElement("i");


        //添加class
        td_1.className = "table__item";
        td_2.className = "table__item";
        td_3.className = "table__item";
        td_4.className = "table__item";
        td_5.className = "table__item";

        input_1.type = "text";
        input_2.type = "text";
        input_3.type = "time";
        input_4.type = "time";
        input_1.className = "form-control";
        input_2.className = "form-control";
        input_3.className = "form-control";
        input_4.className = "form-control";
        input_1.disabled = true;
        input_2.disabled = true;
        input_3.disabled = true;
        input_4.disabled = true;
        input_1.style.cssText = "border: none; background-color: white;";
        input_2.style.cssText = "border: none; background-color: white;";
        input_3.style.cssText = "border: none; background-color: white;";
        input_4.style.cssText = "border: none; background-color: white;";
        input_1.value = location;
        input_2.value = agenda;
        input_3.value = appointtime;
        input_4.value = endtime;

        Icon_1.className = "fas fa-arrow-alt-circle-up";
        Icon_2.className = "fas fa-arrow-alt-circle-down";
        Icon_3.className = "fas fa-edit";
        Icon_4.className = "fas fa-trash-alt";
        Icon_2.style.cssText = "margin-left: 2%;";
        Icon_3.style.cssText = "margin-left: 2%;";
        Icon_4.style.cssText = "margin-left: 2%;";


        //添加元素，形成父子關係
        td_1.appendChild(input_1);
        td_2.appendChild(input_2);
        td_3.appendChild(input_3);
        td_4.appendChild(input_4);
        td_5.appendChild(Icon_1);
        td_5.appendChild(Icon_2);
        td_5.appendChild(Icon_3);
        td_5.appendChild(Icon_4);
        agendaItem.appendChild(td_1);
        agendaItem.appendChild(td_2);
        agendaItem.appendChild(td_3);
        agendaItem.appendChild(td_4);
        agendaItem.appendChild(td_5);


        //添加到頁面上
        document.getElementById('agenda_table').appendChild(agendaItem);

        //增加刪除方法
        Icon_4.onclick = function () {
            if (icon_function_enable) {
                agendaItem.parentNode.removeChild(agendaItem);
            }
        }

        //增加修改方法
        Icon_3.onclick = function (ev) {

            if (icon_function_enable) {
                if (controlEdit == true) {
                    controlEdit = false;

                    input_1.disabled = false;
                    input_2.disabled = false;
                    input_3.disabled = false;
                    input_4.disabled = false;
                    input_1.focus();


                    ev = window.event || ev;
                    ev.stopPropagation ? ev.stopPropagation() : ev.cancelBubble = true;

                    document.onclick = function (event) {

                        if ((event.target != input_1) && (event.target != input_2) && (event.target != input_3) && (event.target != input_4)) {
                            input_1.disabled = true;
                            input_2.disabled = true;
                            input_3.disabled = true;
                            input_4.disabled = true;
                            controlEdit = true;
                        }
                    }
                }
            }
        }


        //增加上移方法
        Icon_1.onclick = function () {

            if (icon_function_enable) {
                if (agendaItem.rowIndex != 2) {

                    var node1 = agendaItem;
                    var node2 = agendaItem.previousElementSibling;

                    var _parent = node1.parentNode;
                    //獲取兩個結點的相對位置
                    var _t1 = node1.nextSibling;
                    var _t2 = node2.nextSibling;

                    //將node2插入到原來node1的位置
                    if (_t1) _parent.insertBefore(node2, _t1);
                    else _parent.appendChild(node2);
                    //將node1插入到原來node2的位置
                    if (_t2) _parent.insertBefore(node1, _t2);
                    else _parent.appendChild(node1);

                }
            }
        }

        //增加下移方法
        Icon_2.onclick = function () {

            if (icon_function_enable) {

                var node1 = agendaItem;
                var node2 = agendaItem.nextElementSibling;

                var _parent = node1.parentNode;
                //獲取兩個結點的相對位置
                var _t1 = node1.nextSibling;
                var _t2 = node2.nextSibling;

                //將node2插入到原來node1的位置
                if (_t1) _parent.insertBefore(node2, _t1);
                else _parent.appendChild(node2);
                //將node1插入到原來node2的位置
                if (_t2) _parent.insertBefore(node1, _t2);
                else _parent.appendChild(node1);

            }
        }
    }
	
	var timeOut1;
	var timeOut2;
	var timeOut3;
	function reload(){
		timeOut3 = setTimeout(function(){app.getDetail();},500);
		timeOut1 = setTimeout(function(){app.getMain();},1000);
		timeOut2 = setTimeout(function(){initial();},1500);
	}
	function clearTimeOut(){
		clearTimeout(timeOut1);
		clearTimeout(timeOut2);
		clearTimeout(timeOut3);
	}
	
	function UnescapeHTML(a) {
        a = "" + a;
        return a.replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&amp;/g, "&").replace(/&quot;/g, '"').replace(/&apos;/g, "'");
    }
	
	$(document).ready(function() {
		reload();
	});
	
	var msg = new Vue({
        el: '#msg',
        data: {
            txt: '',
			messages:[],
			id:0,
			edit:0,
			user:'',
		},
		created(){
			this.getUser();
			this.getMessages();
		},
        methods: {
			edit_msg: function (index, message) {
				if(this.edit == index){
					this.updateMessage(index,message);
					this.edit = -1;
				}else{
					this.edit = index;
				}
            },
			getMessages: function(){
				var token = localStorage.getItem('token');
				var form_Data = new FormData();
				let _this = this;
				_this.clear();
				this.action = 1;//select all
				form_Data.append('jwt', token);
				form_Data.append('action', this.action);

				axios({
					method: 'post',
					headers: {
					'Content-Type': 'multipart/form-data',
					},
					url: 'api/work_calender_message',
					data: form_Data
				})
				.then(function(response) {
					//handle success
					_this.messages = response.data
				})
				.catch(function(response) {
					//handle error
					//alert(JSON.stringify(response));
					console.log(response)
				});
			},
			addMessages:function(message){
				this.action = 2;//add
				var token = localStorage.getItem('token');
				var form_Data = new FormData();
				let _this = this;
				_this.clear();
					form_Data.append('jwt', token);
					form_Data.append('message', message);
					form_Data.append('is_enabled', true);
					form_Data.append('action', this.action);
					form_Data.append('created_by', _this.user);
					axios({
						method: 'post',
						headers: {
							'Content-Type': 'multipart/form-data',
						},
						url: 'api/work_calender_message',
						data: form_Data
					})
						.then(function (response) {
						  //_this.messages.push({id: response.data[0], message: message, is_enabled: "1", created_by: _this.user, created_at: "now" });
							_this.txt='';
						  //this.addDetails(response.data[0]);
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                          
						})
						.catch(function (response) {
                          //handle error
						});
						_this.reloadMessages();
			},
			deleteMessages: function(id){
				this.action = 7;//delete
				var token = localStorage.getItem('token');
				var form_Data = new FormData();
				let _this = this;
				_this.clear();
		
				form_Data.append('jwt', token);
				form_Data.append('id', id);
				form_Data.append('action', _this.action);
				form_Data.append('deleted_by',_this.user);
				axios({
					method: 'post',
					headers: {
						'Content-Type': 'multipart/form-data',
					},
					url: 'api/work_calender_message',
					data: form_Data
				})
				.then(function (response) {
						//handle success
						//_this.items = response.data
				})
				.catch(function (response) {
						//handle error
				});
				_this.reloadMessages();
			},
			updateMessage: function(id,message){
				this.action = 3;//update
				var token = localStorage.getItem('token');
				var form_Data = new FormData();
				let _this = this;
				_this.clear();
                form_Data.append('jwt', token);
				form_Data.append('id', id);
                form_Data.append('is_enabled', true);
				form_Data.append('message', message);
                form_Data.append('action', this.action);
                form_Data.append('updated_by', _this.user);
                axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    url: 'api/work_calender_message',
                    data: form_Data
                })
                .then(function (response) {
                    console.log(response);
                          //handle success
                })
                .catch(function (response) {
                          //handle error
					console.log(response);
                });
				_this.reloadMessages();
			  
			},
			getUser: function() {
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
					_this.user = response.data.username;					
				})
				.catch(function(response) {
						//handle error
				});
			},
			reloadMessages : function(){
				let _this = this;
				_this.myVar = setTimeout(function(){
					_this.getMessages();},500);
			},
			clear : function(){
				let _this = this;
				clearTimeout(_this.myVar)
			},
        }
    })