jQuery(document).ready(function($) {
	var formfield;
	jQuery('.upload_image_button').click(function() {
	jQuery('html').addClass('Image');
	formfield = jQuery(this).prev().attr('name');
	tb_show('', 'media-upload.php?type=image&amp;tab=type&amp;TB_iframe=true');
	return false;});
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){
		if (formfield) {
				fileurl = jQuery('img',html).attr('src');
				jQuery('#'+formfield).val(fileurl);
				tb_remove();
				update_dvd_cover_face();
				jQuery('html').removeClass('Image');
		} else { window.original_send_to_editor(html);}
	};
	/******************************* variables *******************/
			//Preparamos el render
			var modelo3d_;
			if (!window.WebGLRenderingContext) { var Render=new THREE.CanvasRenderer();	} else {
				var Render=new THREE.WebGLRenderer({antialias: true, preserveDrawingBuffer: true});}
			//El escenario
			var Escenario=new THREE.Scene();
			// la Figura 
			var Figura;
			var controls;
			var Ancho=700;
			var Alto=600;
			var Angulo = 45;	
			var Aspecto=Ancho/Alto;
			var cerca=0.1;
			var lejos=10000;
			var animate3d_value=$('#animate3d').prop('checked');
			var init_rotation=$('#grados_3dvd').val();
			
			//La cámara
			var Camara=new THREE.PerspectiveCamera(Angulo,Aspecto,cerca,lejos);
		
			/******************************* inicio *******************/
		function inicio(){
				//Tamaño del render(resultado)
				Render.setSize(Ancho,Alto);
				//Se agrega el render al documento html
				document.getElementById('render').appendChild(Render.domElement);
				//Acercamos la cámara en z es profundidad para ver el punto
				Camara.position.z=3.3;
				Camara.position.y=0;
				//Camara.rotation.y=Math.PI/2;
				Escenario.add(Camara);
				controls=new THREE.OrbitControls(Camara,Render.domElement);
				//cargar el modelo blender json
				var JsonModeloBlender = new THREE.JSONLoader();
				JsonModeloBlender.load(dvdexternaldata.url3dmodel, AgregarModeloBlender );	
		}
		function AgregarModeloBlender( geometry, materials ) {
			textura_geometrias = new THREE.ImageUtils.loadTexture(dvdexternaldata.url3dtexture);
			material_geometrias = new THREE.MeshBasicMaterial({map:textura_geometrias,wireframe:false});
			materials[0]=material_geometrias;
			material2 = new THREE.MeshLambertMaterial({color:0x3E97D6});
			materials[1]=material2;
			material = new THREE.MeshFaceMaterial( materials );
			modelo3d_ = new THREE.Mesh( geometry,material);	
			modelo3d_.position.set(1,0,0);	
			Escenario.add(modelo3d_);
			modelo3d_.position.set(0,0,0);
			 modelo3d_.rotation.y =$('#grados_3dvd').val() * Math.PI / 180;
						
			Luz();
			update_dvd_cover_face();
		}
		function update_dvd_cover_face(){
			var image_1=jQuery('#image_1').val();
			var image_2=jQuery('#image_2').val();
			var image_3=jQuery('#image_3').val();
			textura_geometrias = new THREE.ImageUtils.loadTexture(dvdexternaldata.url3dtexture+"?image_1="+image_1+"&image_2="+image_2+"&image_3="+image_3);
			material_geometrias = new THREE.MeshBasicMaterial({map:textura_geometrias,wireframe:false});
			modelo3d_.material.materials[0]=material_geometrias;
			
			var color_textura=jQuery('#mv_cr_section_color').val();
			var Parametro_color = new THREE.Color(color_textura);
			modelo3d_.material.materials[1]=new THREE.MeshLambertMaterial({color:Parametro_color});
		}
		inicio();
		animacion();
		function Luz(){
			var ambiente = new THREE.AmbientLight( 0x222222 );
			Escenario.add( ambiente);
			var light = new THREE.DirectionalLight( 0xffffff, 0.5);
			light.position.set(10, 10, 10);
			light.position.multiplyScalar( 0.05 );
			Escenario.add( light );
			hemiLight = new THREE.HemisphereLight( 0xffffff, 0xebebeb, 0.6 );
			hemiLight.position.set( 0, 500, 0 );
			hemiLight.shadowCameraVisible = true;
			Escenario.add( hemiLight );
		}						
		function animacion(){
			requestAnimationFrame(animacion);
			render_modelo();
			if((modelo3d_)&&(animate3d_value)){
			modelo3d_.rotation.y=modelo3d_.rotation.y+0.007;
			}
		}
		function render_modelo(){
			controls.update();Render.render(Escenario,Camara);
		}
		jQuery('#btn_print').click(function() {
		window.open( Render.domElement.toDataURL('image/png'), 'IMg3d' );
		});
		
		jQuery('#btn_create').click(function() {
				 $('#loader-ajax').show('slow');
				//window.open( Render.domElement.toDataURL('image/png'), 'mywindow' );
				//alert(Render.domElement.toDataURL('image/png'));
				//return false;
					var	data_image_3dvd=Render.domElement.toDataURL('image/png')
					$.ajax({
					type: 'post',
					url: ajaxurl,
					contentType: 'application/x-www-form-urlencoded',
					data: {
						'action':'save_image_3dvd',
						'post_id': $('#post_ID').val(),
						'image_3dvd' : data_image_3dvd
						
					},
					success:function(data) {
						if(data=="ok"){
						console.log(data);
						if(!$('#save-post').length ){
									$('#publish').click();
									$('#post_status').val("draft");
						}else{
									$('#save-post').click();
						}
						}else{
						 $('#loader-ajax').hide('slow');
						 alert("Error");
						}
						
						
					},
					error: function(errorThrown){
						console.log(errorThrown);
					}
				});

	
				
				
		});
		
		function PrismSetAsThumbnail(id, nonce){  
    var $link = jQuery('a#wp-post-thumbnail-' + id);

    $link.text( setPostThumbnailL10n.saving );
    jQuery.post(ajaxurl, {
        action:"prism_set_thumbnail", post_id: post_id, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
    }, function(str){
        var win = window.dialogArguments || opener || parent || top;  
        $link.text( setPostThumbnailL10n.setThumbnail );
        if ( str == '0' ) {
            alert( setPostThumbnailL10n.error );
        } else {
            jQuery('a.wp-post-thumbnail').show();
            $link.text( setPostThumbnailL10n.done );
            $link.fadeOut( 2000 );

            //display new thumbnail in the columns w/o refresh
            jQuery('#post-'+win.post_id + ' .postimagediv a', win.parent.document).html(str).fadeIn();

            //if successful close thickbox
            win.parent.tb_remove();
        } 
    }
    );
}
// no ajax
		
		
		var myOptions = {change: function(event, ui){
			update_dvd_cover_face();
		}};

		$('#mv_cr_section_color').wpColorPicker(myOptions);
		$('#animate3d').click(function () {
			animate3d_value=this.checked;
		});
		$('#grados_3dvd').on('change', function(){
			$("#valueofrange").html($('#grados_3dvd').val());
			 modelo3d_.rotation.y =$('#grados_3dvd').val() * Math.PI / 180;
		});
			/**************************llamado a las funciones ******************/
});

