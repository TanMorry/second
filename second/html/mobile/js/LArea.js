/*省级的数据，目前定死，也可以用Ajax送数据库调用一次*/

//pcaId = [1,38,578];

//				            url:"http://127.0.0.1/tongrenmiao/?g=mall&m=index&a=getAreaData",
//				            data:{'type':'0'},
//				            dataType:'json',
//				            type:'get',
//				            success:function(data)
//				            {
//				            	console.log('初始化');
//				            	//console.log("init-----------"+JSON.stringify(data));
//                      		_self.setGearTooth(data);
//				            	
//				            },
//				            error:function(data)
//				            {
//				            	//console.log(data);
//				            	//console.log("init-----------"+JSON.stringify(data));
//				            	console.log(data['responseText']);
//				            	//_self.setGearTooth(eval(data['responseText']));
//				            }
//				        });
				            url:"http://127.0.0.1/tongrenmiao/?g=mall&m=index&a=getAreaData",
				            data:{'type':'0'},
				            dataType:'jsonp',
				            type:'get',
				            success:function(data)
				            {
				            	console.log("init-----------"+JSON.stringify(data));
                        		_self.setGearTooth(_self.data[0]);
				            	
				            }
				        });
//              console.log( JSON.stringify(saveData) );
//		            		saveArea.push( ret[i] );
//		            	}
		            		if( saveArea[i].id == id ){
		            			break;
		            		}
		            		for( j = 0 ; j < saveArea[i].child.length ; j++ ){
		            			if( saveArea[i].child[j].id == id ){
		            				for( k = 0 ; k < ret.length ; k++ ){
		            			}
		            		}
		            	}
//						console.log(saveArea);