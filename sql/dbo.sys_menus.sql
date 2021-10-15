USE [wssp]
GO
/****** Object:  Table [dbo].[sys_menus]    Script Date: 15/10/2021 01:42:58 p. m. ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[sys_menus](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[nombre] [nchar](30) NULL,
	[modulo] [nchar](25) NULL,
	[action] [nchar](100) NULL,
	[route] [nchar](50) NULL,
	[descripcion] [varchar](max) NULL,
	[iconClass] [nchar](20) NULL,
	[lv_acceso] [smallint] NULL,
	[orden] [smallint] NULL,
	[activo] [bit] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[sys_menus] ON 

INSERT [dbo].[sys_menus] ([id], [nombre], [modulo], [action], [route], [descripcion], [iconClass], [lv_acceso], [orden], [activo]) VALUES (1, N'Actualizacion de Precios      ', N'inventario               ', N'actualizarPreciosProductos                                                                          ', N'/inventario/updateProducto                        ', NULL, N'fa fa-edit          ', 0, 2, 1)
INSERT [dbo].[sys_menus] ([id], [nombre], [modulo], [action], [route], [descripcion], [iconClass], [lv_acceso], [orden], [activo]) VALUES (2, N'Modulo de Inventario          ', N'inventario               ', N'inventario                                                                                          ', N'/inventario                                       ', NULL, N'fa fa-cubes         ', 0, 1, 1)
INSERT [dbo].[sys_menus] ([id], [nombre], [modulo], [action], [route], [descripcion], [iconClass], [lv_acceso], [orden], [activo]) VALUES (6, N'Actualizacion de Marcas       ', N'inventario               ', N'actualizarMarcaProductos                                                                            ', N'/inventario/updateMarca                           ', NULL, N'fa fa-edit          ', 0, 3, 1)
INSERT [dbo].[sys_menus] ([id], [nombre], [modulo], [action], [route], [descripcion], [iconClass], [lv_acceso], [orden], [activo]) VALUES (7, N'Actualizacion de Colecciones  ', N'inventario               ', N'actualizarColeccionProductos                                                                        ', N'/inventario/updateColeccion                       ', NULL, N'fa fa-edit          ', 0, 4, 1)
SET IDENTITY_INSERT [dbo].[sys_menus] OFF
GO
