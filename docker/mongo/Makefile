build:
	docker pull mongo:4.0.4
	docker tag mongo:4.0.4 sighup/kubeprimer-mongo

run:
	docker rm -f kubeprimer-mongo || true
	docker run -d --name kubeprimer-mongo --network=kubeprimer -p 27017:27017 sighup/kubeprimer-mongo
