from django.urls import path
from .views import *

urlpatterns = [
    path('customer', customers_list, name='customers_list'),
    path('add/', add_customer, name='add_customer'),
    path('edit/<int:pk>/', edit_customer, name='edit_customer'),
    path('delete/<int:pk>/', delete_customer, name='delete_customer'),
    path('dashboard/', dashboard, name='dashboard'),
    path('', login_view, name='login'),
    path('logout/', logout_view, name='logout'),
    path('profile/', profile_view, name='profile'),
    path('profile/edit/', edit_profile, name='edit_profile'),
]

