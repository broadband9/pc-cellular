from django.urls import path
from .views import dashboard, login_view, logout_view, customers_list, add_customer, edit_customer, delete_customer

urlpatterns = [
    path('', customers_list, name='customers_list'),
    path('add/', add_customer, name='add_customer'),
    path('edit/<int:pk>/', edit_customer, name='edit_customer'),
    path('delete/<int:pk>/', delete_customer, name='delete_customer'),
    path('dashboard/', dashboard, name='dashboard'),
    path('login/', login_view, name='login'),
    path('logout/', logout_view, name='logout'),
]

